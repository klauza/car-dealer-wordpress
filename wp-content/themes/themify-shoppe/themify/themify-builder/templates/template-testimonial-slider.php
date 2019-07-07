<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Testimonial
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
$fields_default = array(
    'mod_title_testimonial' => '',
    'layout_testimonial' => 'image-top',
    'tab_content_testimonial' => '',
    'css_testimonial' => ''
);
$fields_args = wp_parse_args($mod_settings, $fields_default);
$fields_args['css_slider'] = $fields_args['css_testimonial'];
$fields_args['mod_title_slider'] = $fields_args['mod_title_testimonial'];
$fields_args['layout_slider'] = $fields_args['layout_testimonial']!==''?$fields_args['layout_testimonial']:$fields_default['layout_testimonial'];
$fields_args['layout_display_slider'] = 'content';
unset($mod_settings, $fields_args['css_testimonial'], $fields_args['mod_title_testimonial'], $fields_args['layout_testimonial']);
self::retrieve_template('template-slider.php', array(
    'module_ID' => $module_ID,
    'mod_name' => $mod_name,
    'mod_settings' => $fields_args
        ), '', '', true);
