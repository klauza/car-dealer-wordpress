<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Slider Text
 * 
 * Access original fields: $settings
 * @author Themify
 */
if (!empty($settings['video_content_slider'])):?>
    <?php foreach ($settings['video_content_slider'] as $video): ?>
        <li>
            <div class="slide-inner-wrap"<?php if ($settings['margin'] !== ''): ?> style="<?php echo $settings['margin']; ?>"<?php endif; ?>>
                <?php if (!empty($video['video_url_slider'])): ?>
                    <?php $video_maxwidth = !empty($video['video_width_slider']) ? $video['video_width_slider'] : ''; ?>
                    <div class="slide-image video-wrap"<?php echo '' !== $video_maxwidth ? 'style="max-width:' . $video_maxwidth . 'px;"' : ''; ?>>
                        <?php echo str_replace('frameborder="0"', '', wp_oembed_get(esc_url($video['video_url_slider']))); ?>
                    </div><!-- /video-wrap -->
                <?php endif; ?>

                <div class="slide-content">
                    <h3 class="slide-title">
                        <?php if (!empty($video['video_title_link_slider'])): ?>
                            <a href="<?php echo esc_url($video['video_title_link_slider']); ?>"<?php echo 'yes' === $settings['open_link_new_tab_slider'] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo $video['video_title_slider']; ?></a>
                        <?php elseif (isset($video['video_title_slider'])) : ?>
                            <?php echo $video['video_title_slider']; ?>
                        <?php endif; ?>
                    </h3>
                    <div class="video-caption">
                        <?php
                        if (isset($video['video_caption_slider'])) {
                            echo apply_filters('themify_builder_module_content', $video['video_caption_slider']);
                        }
                        ?>
                    </div>
                    <!-- /video-caption -->
                </div><!-- /video-content -->
            </div>
        </li>
    <?php endforeach; // end loop video  ?>
<?php endif; ?>