<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Accordion
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_accordion' => '',
        'layout_accordion' => 'plus-icon-button',
        'expand_collapse_accordion' => 'toggle',
        'color_accordion' => '',
        'accordion_appearance_accordion' => '',
        'content_accordion' => array(),
        'animation_effect' => '',
        'icon_accordion' => '',
        'icon_color_accordion' => '',
        'icon_active_accordion' => '',
        'icon_active_accordion' => '',
        'icon_active_color_accordion' => '',
        'css_accordion' => ''
    );

    if (isset($mod_settings['accordion_appearance_accordion'])) {
        $mod_settings['accordion_appearance_accordion'] = self::get_checkbox_data($mod_settings['accordion_appearance_accordion']);
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $fields_args['css_accordion'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class,
        'data-behavior' => $fields_args['expand_collapse_accordion']
            ), $fields_args, $mod_name, $module_ID);

    $ui_class = implode(' ', array('ui', 'module-' . $mod_name, $fields_args['layout_accordion'], $fields_args['accordion_appearance_accordion'], $fields_args['color_accordion']));
    ?>
    <!-- module accordion -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_accordion'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_accordion'], $fields_args) . $fields_args['after_title']; ?>
        <?php endif; ?>

        <ul class="<?php echo $ui_class ?>">
            <?php
            $content_accordion = array_filter($fields_args['content_accordion']);
            foreach ($content_accordion as $content):
                $content = wp_parse_args($content, array(
                    'title_accordion' => '',
                    'default_accordion' => 'closed',
                    'text_accordion' => '',
                ));
                ?>
                <li <?php if ($content['default_accordion'] === 'open') echo 'class="builder-accordion-active"'; ?>>
                    <div class="accordion-title">
                        <a href="#">
                            <?php if ($fields_args['icon_accordion'] !== '') : ?><i class="accordion-icon <?php echo themify_get_icon($fields_args['icon_accordion']); ?>"></i><?php endif; ?>
                            <?php if ($fields_args['icon_active_accordion'] !== '') : ?><i class="accordion-active-icon fa <?php echo $fields_args['icon_active_accordion']; ?>"></i><?php endif; ?>
                            <?php echo $content['title_accordion']; ?>
                        </a>
                    </div>
                    <div class="accordion-content clearfix <?php if ($content['default_accordion'] !== 'open'): ?> default-closed<?php endif; ?>">
                        <?php
                        if ($content['text_accordion']!=='') {
                            echo apply_filters('themify_builder_module_content', $content['text_accordion']);
                        }
                        ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
    <!-- /module accordion -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>