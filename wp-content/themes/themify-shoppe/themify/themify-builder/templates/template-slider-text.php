<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Slider Text
 * 
 * Access original fields: $settings
 * @author Themify
 */
if (!empty($settings['text_content_slider'])):?>
    <?php foreach ($settings['text_content_slider'] as $content): ?>
        <li>
            <div class="slide-inner-wrap"<?php if ($settings['margin'] !== ''): ?> style="<?php echo $settings['margin']; ?>"<?php endif; ?>>
                <div class="slide-content">
                    <?php
                    if (isset($content['text_caption_slider'])) {
                        echo apply_filters('themify_builder_module_content', $content['text_caption_slider']);
                    }
                    ?>
                </div><!-- /slide-content -->
            </div>
        </li>
    <?php endforeach; ?>
<?php endif; ?>