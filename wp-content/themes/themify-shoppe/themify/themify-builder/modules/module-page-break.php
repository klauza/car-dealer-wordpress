<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Page break
 * Description: Page breaker and pagination
 */
class TB_Page_Break_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Page Break', 'themify'),
			'slug' => 'page-break'
		));
	}

	public function get_options() {
		return array();
	}

	public function get_default_settings() {
		return array();
	}
	

	public function get_styling() {
		return array();
	}

	protected function _visual_template() { 
		?>
		<div class="module module-<?php echo $this->slug ; ?>">
		<?php _e('PAGE BREAK - ', 'themify') ?>
		<span class="page-break-order"></span>
		</div>
	<?php
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Page_Break_Module' );
