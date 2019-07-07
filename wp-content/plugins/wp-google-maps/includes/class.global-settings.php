<?php

namespace WPGMZA;



require_once(plugin_dir_path(__DIR__) . 'lib/codecabin/class.settings.php');

class GlobalSettings extends \codecabin\Settings
{
	const TABLE_NAME = 'wpgmza_global_settings';
	const LEGACY_TABLE_NAME = 'WPGMZA_OTHER_SETTINGS';
	
	private $updatingLegacySettings = false;
	
	public function __construct()
	{
		$self = $this;
		
		$legacy_settings_exist = (get_option(GlobalSettings::LEGACY_TABLE_NAME) ? true : false);
		$settings_exist = (get_option(GlobalSettings::TABLE_NAME) ? true : false);
		
		if($legacy_settings_exist && !$settings_exist)
			$this->migrate();
		
		\codecabin\Settings::__construct(GlobalSettings::TABLE_NAME);
		
		$this->wpgmza_google_maps_api_key = get_option('wpgmza_google_maps_api_key');
		
		if(!$legacy_settings_exist && !$settings_exist)
			$this->install();
		
		// Legacy Pro support. Users with older Pro will lose settings 
		add_filter('pre_update_option_WPGMZA_OTHER_SETTINGS', array($this, 'onPreUpdateLegacySettings'), 10, 2);
	}
	
	public function __get($name)
	{
		if($name == 'useLegacyHTML')
			return true;
		
		return \codecabin\Settings::__get($name);
	}
	
	// TODO: This should inherit from Factory when traits are available
	public static function createInstance()
	{
		$class = get_called_class();
		$args = func_get_args();
		$count = count($args);
		$filter = "wpgmza_create_$class";
		
		if(empty($args))
			$filter_args = array($filter, null);
		else
			$filter_args = array_merge(array($filter), $args);
		
		$override = call_user_func_array('apply_filters', $filter_args);
		
		if($override)
			return $override;
		
		$reflect = new \ReflectionClass($class);
		$instance = $reflect->newInstanceArgs($args);
		
		return $instance;
	}
	
	public function getDefaults()
	{
		$settings = apply_filters('wpgmza_plugin_get_default_settings', array(
			'engine' 				=> 'google-maps',
			'google_maps_api_key'	=> get_option('wpgmza_google_maps_api_key'),
			'default_marker_icon'	=> Marker::DEFAULT_ICON,
			'developer_mode'		=> !empty($this->legacySettings['developer_mode'])
		));
		
		return $settings;
	}
	
	public function onPreUpdateLegacySettings($new_value, $old_value)
	{
		// Merge legacy settings into this settings
		if(!$this->updatingLegacySettings)
			$this->set($new_value);
		
		return $new_value;
	}
	
	protected function update()
	{
		/*echo "<pre>";
		debug_print_backtrace();
		echo "</pre>";*/
		
		\codecabin\Settings::update();
		
		// Legacy Pro support
		$this->updatingLegacySettings = true;
		
		//var_dump($this->wpgmza_settings_map_full_screen_control);
		
		//if(empty($this->wpgmza_settings_map_full_screen_control))
			//throw new \Exception('why');
		
		$legacy = $this->toArray();
		
		//var_dump($legacy['wpgmza_settings_map_full_screen_control']);
		
		//var_dump("Updating " . GlobalSettings::LEGACY_TABLE_NAME, $legacy);
		
		//if(empty($legacy['wpgmza_settings_map_full_screen_control']))
			//throw new \Exception('Can you not');
		
		update_option(GlobalSettings::LEGACY_TABLE_NAME, $legacy);
		
		//var_dump("Read back ", get_option(GlobalSettings::LEGACY_TABLE_NAME));
		
		$this->updatingLegacySettings = false;
	}
	
	protected function install()
	{
		$this->set( $this->getDefaults() );
	}
	
	protected function migrate()
	{
		$legacy = get_option(GlobalSettings::LEGACY_TABLE_NAME);
		
		$json = json_encode($legacy);
		
		update_option(GlobalSettings::TABLE_NAME, $json);
	}
	
	public function jsonSerialize()
	{
		$src = \codecabin\Settings::jsonSerialize();
		$data = clone $src;
		
		if(isset($data->wpgmza_settings_ugm_email_address))
			unset($data->wpgmza_settings_ugm_email_address);
		
		return $data;
	}
	
	public function toArray()
	{
		$src = \codecabin\Settings::toArray();
		$data = (array)$src;
		
		if(isset($data['wpgmza_settings_ugm_email_address']))
			unset($data['wpgmza_settings_ugm_email_address']);
		
		return $data;
	}
}
