<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Buttons
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_button' => '',
        'buttons_size' => '',
        'buttons_shape' => 'circle',
        'buttons_style' => 'solid',
        'fullwidth_button' => '',
		'nofollow_link'=> '',
        'download_link'=> '',
        'display' => 'buttons-horizontal',
        'content_button' => array(),
        'animation_effect' => '',
        'css_button' => ''
    );

	/* for old button style args*/
	if ( isset($mod_settings['buttons_style']) && in_array( $mod_settings['buttons_style'], array('circle', 'rounded', 'squared' )) ) {
		$mod_settings['buttons_shape'] = $mod_settings['buttons_style'];
		unset($mod_settings['buttons_style']);
	}
	/* End of old button style args */
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $fields_args['css_button'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );
    $ui_class = implode(' ', array('module-' . $mod_name, $fields_args['buttons_size'], $fields_args['buttons_style'], $fields_args['buttons_shape']));

    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);
    ?>
    <!-- module buttons -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_button'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_button'], $fields_args) . $fields_args['after_title']; ?>
        <?php endif; ?>

        <div class="<?php echo $ui_class; ?>">
            <?php
            $content_button = array_filter($fields_args['content_button']);

            foreach ($content_button as $content):
                $content = wp_parse_args($content, array(
                    'label' => '',
                    'link' => '',
                    'icon' => '',
                    'icon_alignment' => 'left',
                    'link_options' => false,
                    'lightbox_width' => '',
                    'lightbox_height' => '',
                    'lightbox_width_unit' => 'px',
                    'lightbox_height_unit' => 'px',
                    'button_color_bg' => false
                ));
               
                $link_css_clsss = array('ui builder_button');
                $link_attr = array();

                if ($content['link_options'] === 'lightbox') {
                    $link_css_clsss[] = 'themify_lightbox';

                    if ($content['lightbox_width']!=='' || $content['lightbox_height']!=='') {
                        $lightbox_settings = array();
                        if($content['lightbox_width']!==''){
                            $lightbox_settings[] = $content['lightbox_width'].$content['lightbox_width_unit'];
                        }
                        if($content['lightbox_height']!==''){
                            $lightbox_settings[] = $content['lightbox_height'].$content['lightbox_height_unit'];
                        }
                        $link_attr[] = sprintf('data-zoom-config="%s"', implode('|', $lightbox_settings));
                    }
                } elseif ($content['link_options'] === 'newtab') {
                    $link_attr[] = 'target="_blank" rel="noopener"';
                }

                if (!empty($content['button_color_bg'])) {
                    $link_css_clsss[] = $content['button_color_bg'];
                }

                if ( !empty($fields_args['nofollow_link']) && $fields_args['nofollow_link'] == 'yes' && $content['link_options'] !== 'newtab' ){
					$link_attr[] = 'rel="nofollow"';
                }

                if ( !empty($fields_args['download_link']) && $fields_args['download_link'] == 'yes'  ){
					$link_attr[] = 'download';
                }
                ?>
                <div class="module-buttons-item <?php echo $fields_args['fullwidth_button']?> <?php echo $fields_args['display']?>">
					<?php
						$button_content = sprintf( '<span>%s</span>', $content['label'] );

						if( $content['icon'] ) {
							$button_content = sprintf( $content['icon_alignment'] === 'right' ? '%2$s %1$s' : '%1$s %2$s'
								, sprintf( '<i class="%s"></i>', themify_get_icon($content['icon'] )), $button_content );
						}

						if( $content['link'] ) {
							printf( '<a href="%s" class="%s" %s>%s</a>'
								, esc_url( $content['link'] )
								, implode( ' ', $link_css_clsss )
								, implode( ' ', $link_attr )
								, $button_content );
						} else {
							echo $button_content;
						}
					?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- /module buttons -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>