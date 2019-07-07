<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Tab
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):
    $fields_default = array(
        'mod_title_tab' => '',
        'layout_tab' => 'minimal',
        'style_tab' => 'default',
        'color_tab' => '',
        'tab_appearance_tab' => '',
        'tab_content_tab' => array(),
        'css_tab' => '',
        'animation_effect' => '',
		'allow_tab_breakpoint' => '',
		'tab_breakpoint'=>''
    );

    if (isset($mod_settings['tab_appearance_tab'])) {
        $mod_settings['tab_appearance_tab'] = self::get_checkbox_data($mod_settings['tab_appearance_tab']);
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $tab_id = $module_ID . '-' . $builder_id;
    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, 'ui', $fields_args['layout_tab'], 'tab-style-' . $fields_args['style_tab'], $fields_args['tab_appearance_tab'], $fields_args['color_tab'], $fields_args['css_tab'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $tab_id,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);

    if ( '' !== $fields_args['allow_tab_breakpoint'] && '' !== $fields_args['tab_breakpoint'] ){
		$container_props['data-tab-breakpoint'] = $fields_args['tab_breakpoint'];
    ?>


    <?php } ?>

    <!-- module tab -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_tab'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_tab'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>

        <div class="builder-tabs-wrap">
            <span class="tab-nav-current-active"><?php echo $fields_args['tab_content_tab'][0]['title_tab'] ?></span>
            <ul class="tab-nav">
                <?php foreach ($fields_args['tab_content_tab'] as $k => $tab): ?>
                    <li <?php echo 0 === $k ? 'aria-expanded="true"' : 'aria-expanded="false"'; ?>>
                        <a href="#tab-<?php echo esc_attr($tab_id . '-' . $k); ?>">
                            <?php if (isset($tab['icon_tab'])) : ?><i class="fa <?php echo $tab['icon_tab']; ?>"></i><?php endif; ?>
                            <span><?php echo isset($tab['title_tab']) ? $tab['title_tab'] : ''; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php foreach ($fields_args['tab_content_tab'] as $k => $tab): ?>
                <div id="tab-<?php echo esc_attr($tab_id . '-' . $k); ?>" class="tab-content" <?php echo $k === 0 ? 'aria-hidden="false"' : 'aria-hidden="true"' ?>>
                    <?php
                        if (isset($tab['text_tab'])) {
                            echo apply_filters('themify_builder_module_content', $tab['text_tab']);
                        }
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- /module tab -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>