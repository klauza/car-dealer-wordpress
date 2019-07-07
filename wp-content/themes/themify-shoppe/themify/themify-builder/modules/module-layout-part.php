<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Module Name: Layout Part
 * Description: Layout Part Module
 */

class TB_Layout_Part_Module extends Themify_Builder_Component_Module {

    function __construct() {
        parent::__construct(array(
            'name' => __('Layout Part', 'themify'),
            'slug' => 'layout-part'
        ));
    }

    public function get_options() {
        return array(
            array(
                'id' => 'mod_title_layout_part',
                'type' => 'text',
                'label' => __('Module Title', 'themify'),
                'class' => 'large',
                'render_callback' => array(
                        'live-selector'=>'.module-title'
                )
            ),
            array(
                'id' => 'selected_layout_part',
                'type' => 'layout_part_select',
                'label' => __('Select Layout Part', 'themify'),
                'is_premium'=>Themify_Builder_Model::is_premium()
            ),
            // Additional CSS
            array(
                'type' => 'separator',
                'meta' => array('html' => '<hr/>')
            ),
            array(
                'id' => 'add_css_layout_part',
                'type' => 'text',
                'label' => __('Additional CSS Class', 'themify'),
                'help' => sprintf('<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify')),
                'class' => 'large exclude-from-reset-field'
            ),
        );
    }

    public function get_styling() {
        return array(
            array(
                'type' => 'tabs',
                'id' => 'module-styling',
                'tabs' => array(
                    'module-title' => array(
                        'label' => __('Module Title', 'themify'),
                        'fields' => $this->module_title_custom_style()
                    )
                )
            ),
        );
    }
    
    public function get_visual_type() {
        return 'ajax';            
    }

    public function get_animation() {
        return array();
    }

}
function themify_builder_field_layout_part_select( $field, $mod_name ) {
        themify_builder_module_settings_field(array(
             array(
                'id' => $field['id'],
                'type' => 'select',
                'label' =>$field['label'],
                'render_callback'=>array('control_type'=>'layout_part'),
                'required' => array(
                    'rule' => 'not_empty',
                    'message' => esc_html__( "Please select a Layout Part. If you don't have any, add a new Layout Part", 'themify' )
                ),
                'options' => array(),
            )
        ),$mod_name);
        if(Themify_Builder_Model::is_premium()){
            global $Themify_Builder_Layouts;
            printf('<a href="%s" target="_blank" class="add_new"><span class="tb_icon add"></span> %s</a>', esc_url(add_query_arg('post_type', $Themify_Builder_Layouts->layout_part->post_type_name, admin_url('post-new.php'))), __('New Layout Part', 'themify'));
            printf('<a href="%s" target="_blank" class="add_new"><span class="tb_icon ti-folder"></span> %s</a>', esc_url(add_query_arg('post_type', $Themify_Builder_Layouts->layout_part->post_type_name, admin_url('edit.php'))), __('Manage Layout Part', 'themify'));
        }
}
    
///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module('TB_Layout_Part_Module');
