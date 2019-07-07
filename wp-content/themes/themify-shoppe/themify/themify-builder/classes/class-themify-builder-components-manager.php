<?php

class Themify_Builder_Components_Manager {
	
	/**
	 * @var $_component_types;
	 */
	private static $_component_types=null;
        
        private function __construct() {}

	public static function init() {
            include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/components/base.php' );
            include_once( THEMIFY_BUILDER_CLASSES_DIR . '/premium/class-themify-builder-include.php');
            include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/components/row.php' );
            include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/components/subrow.php' );
            include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/components/column.php' );
            include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/components/module.php');
	}
	private static function register_component_type( Themify_Builder_Component_Base $component ) {
		self::$_component_types[ $component->get_name() ] = $component;
	}

	public static function get_component_types( $component_name = null ) {
		if ( self::$_component_types===null ) {
                    self::load();
		}

		if ( null !== $component_name ) {
			return isset( self::$_component_types[ $component_name ] ) ? self::$_component_types[ $component_name ] : null;
		}

		return self::$_component_types;
	}

	private static function load() {
		self::$_component_types = array();

		foreach ( array( 'row', 'subrow', 'column' ) as $component_name ) {
			$class_name = 'Themify_Builder_Component_' . ucfirst( $component_name );
			self::register_component_type( new $class_name() );
		}
	}

	public static function render_components_form_content($echo=FALSE) {
            $return = array();
            $components = array_merge( self::get_component_types(),Themify_Builder_Model::$modules);
            foreach ($components as $component_type ) {
                if(!$echo){
                    $return[$component_type->get_name()] = preg_replace('!\s+!', ' ', $component_type->print_template_form($echo));
                }
                else{
                    $component_type->print_template_form($echo);
                }
            }
            return $return;
	}
}