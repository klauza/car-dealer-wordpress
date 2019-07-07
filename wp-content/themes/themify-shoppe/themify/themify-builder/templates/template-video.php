<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Video
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_video' => '',
        'style_video' => '',
        'url_video' => '',
        'autoplay_video' => 'no',
        'width_video' => '',
        'unit_video' => 'px',
        'title_video' => '',
        'title_link_video' => false,
        'caption_video' => '',
        'css_video' => '',
        'animation_effect' => ''
    );

    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $video_maxwidth = $fields_args['width_video'] !== '' ? $fields_args['width_video'] . $fields_args['unit_video'] : '';
    $video_autoplay_css = $fields_args['autoplay_video'] === 'yes' ? 'video-autoplay' : '';
    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $fields_args['style_video'], $fields_args['css_video'], $animation_effect, $video_autoplay_css
                    ), $mod_name, $module_ID, $fields_args)
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);

    add_filter('oembed_result', array('TB_Video_Module', 'modify_youtube_embed_url'), 10, 3);
    ?>

    <!-- module video -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_video'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_video'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>

        <div class="video-wrap"<?php echo '' !== $video_maxwidth ? ' style="max-width:' . $video_maxwidth . ';"' : ''; ?>>
			<?php
			$video = wp_oembed_get( esc_url( $fields_args['url_video'] ) );
            if ($video) {
				$video = str_replace( 'frameborder="0"', '', $video );
				$video = str_replace( '?&', '&', $video );

                if ($fields_args['autoplay_video'] === 'yes') {
                    $video = preg_replace_callback('/src=["\'](http[^"\']*)["\']/', array('TB_Video_Module', 'autoplay_callback'), $video);
                }
                echo $video;
            } else {
                global $wp_embed;
                $video = $wp_embed->run_shortcode('[embed]' . $fields_args['url_video'] . '[/embed]');
                if ($video) {
                    if ($fields_args['autoplay_video'] === 'yes') {
                        $video = str_replace('src', 'autoplay="on" src', $video);
                    }
                    echo do_shortcode($video);
                }
            }
            ?>
        </div>
        <!-- /video-wrap -->

        <?php if ('' !== $fields_args['title_video'] || '' !== $fields_args['caption_video']): ?>
            <div class="video-content">
                <?php if ('' !== $fields_args['title_video']): ?>
                    <h3 class="video-title">
                        <?php if ($fields_args['title_link_video']) : ?>
                            <a href="<?php echo esc_url($fields_args['title_link_video']); ?>"><?php echo $fields_args['title_video']; ?></a>
                        <?php else: ?>
                        <?php echo $fields_args['title_video']; ?>
                        <?php endif; ?>
                    </h3>
                <?php endif; ?>

                <?php if ('' !== $fields_args['caption_video']): ?>
                    <div class="video-caption">
                        <?php echo apply_filters('themify_builder_module_content', $fields_args['caption_video']); ?>
                    </div>
                    <!-- /video-caption -->
                <?php endif; ?>
            </div>
            <!-- /video-content -->
        <?php endif; ?>
    </div>
    <!-- /module video -->
    <?php remove_filter('oembed_result', array('TB_Video_Module', 'modify_youtube_embed_url'), 10, 3); ?>
<?php endif; ?>
<?php TFCache::end_cache(); ?>