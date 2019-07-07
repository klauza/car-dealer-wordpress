<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Image
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $chart_vars = apply_filters('themify_chart_init_vars', array(
        'trackColor' => 'rgba(0,0,0,.1)',
        'size' => 150
    ));

    $fields_default = array(
        'mod_title_feature' => '',
        'title_feature' => '',
        'overlap_image_feature' => '',
        'overlap_image_width' => '',
        'overlap_image_height' => '',
        'layout_feature' => 'icon-top',
        'content_feature' => '',
        'circle_percentage_feature' => '',
        'circle_color_feature' => '#de5d5d',
        'circle_stroke_feature' => '',
        'icon_type_feature' => 'icon',
        'image_feature' => '',
        'icon_feature' => '',
        'icon_color_feature' => '#000',
        'icon_bg_feature' => '',
        'circle_size_feature' => 'medium',
        'custom_circle_size_feature' => '',
        'link_feature' => '',
        'feature_download_link'=>'',
        'link_options' => false,
        'lightbox_width' => '',
        'lightbox_height' => '',
        'lightbox_width_unit' => 'px',
        'lightbox_height_unit' => 'px',
        'css_feature' => '',
        'animation_effect' => ''
    );
    $fields_args = wp_parse_args($mod_settings, $fields_default);
	$fields_args['lightbox_width_unit'] = $fields_args['lightbox_width_unit'] ? $fields_args['lightbox_width_unit'] : 'px';
	$fields_args['lightbox_height_unit'] = $fields_args['lightbox_height_unit'] ? $fields_args['lightbox_height_unit'] : 'px';
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    /* configure the chart size based on the option */
    if ($fields_args['circle_size_feature'] === 'large') {
        $chart_vars['size'] = 200;
    } elseif ($fields_args['circle_size_feature'] === 'medium') {
        $chart_vars['size'] = 150;
    } elseif ($fields_args['circle_size_feature'] === 'small') {
        $chart_vars['size'] = 100;
    }else{
		$chart_vars['size'] = isset($fields_args['custom_circle_size_feature']) ? $fields_args['custom_circle_size_feature'] : 150;
    }

    $fields_args['circle_percentage_feature'] = str_replace('%', '', $fields_args['circle_percentage_feature']); // remove % if added by user

    if ($fields_args['circle_percentage_feature'] === '') {
        $chart_class = 'no-chart';
        $fields_args['circle_percentage_feature'] = 0;
        $chart_vars['trackColor'] = 'rgba(0,0,0,0)'; // transparent
    } else {
        if ($fields_args['circle_percentage_feature'] > 100) {
            $fields_args['circle_percentage_feature'] = '100';
        }
        $chart_class = 'with-chart';
    }
    if ('' !== $fields_args['overlap_image_feature']) {
        $chart_class .= ' with-overlay-image';
    }
    $link_type = $link_attr = '';
    if (!empty($fields_args['link_options'])) {
        $link_type = 'regular';
        if ($fields_args['link_options'] === 'lightbox') {
            $link_type = 'lightbox';

            if ($fields_args['lightbox_width'] !== '' || $fields_args['lightbox_height'] !== '') {
                $lightbox_settings = array();
                $lightbox_settings[] = $fields_args['lightbox_width'] !== '' ? $fields_args['lightbox_width'] . $fields_args['lightbox_width_unit'] : '';
                $lightbox_settings[] = $fields_args['lightbox_height'] !== '' ? $fields_args['lightbox_height'] . $fields_args['lightbox_height_unit'] : '';

                $link_attr = sprintf('data-zoom-config="%s"', implode('|', $lightbox_settings));
            }
        } elseif ($fields_args['link_options'] === 'newtab') {
            $link_type = 'newtab';
        }
    }

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $chart_class, 'layout-' . $fields_args['layout_feature'], 'size-' . $fields_args['circle_size_feature'], $fields_args['css_feature'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);
    ?>
    <!-- module feature -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php
        // DYNAMIC STYLE
        $insetColor = $fields_args['icon_bg_feature'] !== '' ? esc_attr(Themify_Builder_Stylesheet::get_rgba_color($fields_args['icon_bg_feature'])) : '';
        $style = '<style type="text/css">';
        if ($fields_args['circle_stroke_feature']) {
            $fields_args['circle_stroke_feature'] = (int) $fields_args['circle_stroke_feature'];
            $circleBackground = $chart_vars['trackColor'];
            $circleColor = esc_attr(Themify_Builder_Stylesheet::get_rgba_color($fields_args['circle_color_feature']));
            $style.="#{$module_ID} .module-feature-chart-html5 {
					box-shadow: inset 0 0 0 " . $fields_args['circle_stroke_feature'] . "px {$circleBackground};
				}
				#{$module_ID} .chart-loaded.chart-html5-fill {
					box-shadow: inset 0 0 0 " . $fields_args['circle_stroke_feature'] . "px {$circleColor};
				}";
        }
        if ($insetColor !== '') {
            $style.="#{$module_ID} .chart-html5-inset {background-color: {$insetColor}}";
        }
        $style.='</style>';
        echo $style;
        ?>

        <?php if ($fields_args['mod_title_feature'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_feature'], $fields_args) . $fields_args['after_title']; ?>
        <?php endif; ?>

        <div class="module-feature-image">
            <?php
            if ('' !== $fields_args['overlap_image_feature']) {
                echo themify_get_image('src=' . $fields_args['overlap_image_feature'] . '&w=' . $fields_args['overlap_image_width'] . '&h=' . $fields_args['overlap_image_height'] . '&ignore=true');
            };


			if ( !empty($fields_args['feature_download_link']) && $fields_args['feature_download_link'] == 'yes'  ){
				$link_attr .= ' download';
			}
            ?>

            <?php if ('' !== $fields_args['link_feature']) : ?>
                <a href="<?php echo esc_url($fields_args['link_feature']); ?>" <?php
                if ('lightbox' === $link_type) : echo 'class="themify_lightbox"';
                elseif ('newtab' === $link_type):echo 'target="_blank" rel="noopener"';
                endif;
                ?> <?php echo $link_attr; ?>>
                   <?php endif; ?>

                <div class="module-feature-chart-html5"
                <?php if (!empty($fields_args['circle_percentage_feature'])): ?>
                         data-progress="0"
                         data-progress-end="<?php esc_attr_e($fields_args['circle_percentage_feature']) ?>"
                         data-size="<?php echo $chart_vars['size']; ?>"
                     <?php endif; ?>
                     >
                    <div class="chart-html5-circle">
                        <?php if (!empty($fields_args['circle_percentage_feature'])): ?>
                            <div class="chart-html5-mask chart-html5-full">
                                <div class="chart-html5-fill"></div>
                            </div>
                            <div class="chart-html5-mask chart-html5-half">
                                <div class="chart-html5-fill"></div>
                            </div>
                        <?php endif; ?>
                        <div class="chart-html5-inset<?php if ('icon' === $fields_args['icon_type_feature'] && '' !== $fields_args['icon_feature']) echo ' chart-html5-inset-icon' ?>">

                            <?php if (strpos($fields_args['icon_type_feature'], 'image') !== false && $fields_args['image_feature'] !== '') : ?>
                                <?php $alt = ( $alt_text = Themify_Builder_Model::get_alt_by_url($fields_args['image_feature']) ) ? $alt_text : $fields_args['title_feature']; ?>
                                <img src="<?php echo esc_url($fields_args['image_feature']); ?>" alt="<?php echo esc_attr($alt); ?>" />
                            <?php else : ?>
                                <?php if ('' !== $insetColor) : ?><div class="module-feature-background" style="background: <?php echo $insetColor; ?>"></div><?php endif; ?>
                                <?php if ('' !== $fields_args['icon_feature']) : ?><i class="module-feature-icon <?php echo esc_attr(themify_get_icon($fields_args['icon_feature'])); ?>" style="color: <?php echo esc_attr(Themify_Builder_Stylesheet::get_rgba_color($fields_args['icon_color_feature'])); ?>"></i><?php endif; ?>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <?php if ('' !== $fields_args['link_feature']) : ?>
                </a>
            <?php endif; ?>

        </div>

        <div class="module-feature-content">
			<?php
				if ( ! empty( $fields_args['title_feature'] ) ) {
					$link_attr .= $link_type === 'newtab' ? ' target="_blank" rel="noopener"' : '';
					$link_attr .= $link_type === 'lightbox' ? ' class="themify_lightbox"' : '';

					$title_feature = ! empty( $fields_args['link_feature'] )
						? sprintf( '<a href="%s"%s>%s</a>'
							, esc_url( $fields_args['link_feature'] )
							, $link_attr
							, $fields_args['title_feature'] )
						: $fields_args['title_feature'];

					printf( '<h3 class="module-feature-title">%s</h3>', $title_feature );
				}
			?>
            <?php echo apply_filters('themify_builder_module_content', $fields_args['content_feature'] !== '' ? do_shortcode($fields_args['content_feature']) : '' ); ?>
            </div>

    </div>
    <!-- /module feature -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>