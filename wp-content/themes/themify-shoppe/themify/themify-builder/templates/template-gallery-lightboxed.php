<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Gallery Lightboxed
 * 
 * Access original fields: $fields_args
 * @author Themify
 */
$alt = isset($settings['gallery_images'][0]->post_excerpt) ? $settings['gallery_images'][0]->post_excerpt : '';

/* if no thumbnail is set   for the gallery, use the first image */
if (empty($settings['thumbnail_gallery'])) {
    $settings['thumbnail_gallery'] = wp_get_attachment_url($settings['gallery_images'][0]->ID);
}
$thumbnail = themify_get_image("ignore=true&src={$settings['thumbnail_gallery']}&w={$settings['thumb_w_gallery']}&h={$settings['thumb_h_gallery']}&alt={$alt}");
foreach ($settings['gallery_images'] as $key => $image):
    ?>
    <dl class="gallery-item"<?php if($key!==0):?> style="display: none;"<?php endif;?>>
        <?php
        $link = wp_get_attachment_url($image->ID);
        $img = wp_get_attachment_image_src($image->ID, 'full');
        $alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
        $title = $image->post_title;
        $caption = $image->post_excerpt;
        if (!empty($link)):
            ?>
            <dt class="gallery-icon"><a href="<?php echo esc_url($link) ?>" title="<?php  esc_attr_e($title) ?>">
            <?php endif; ?>
            <?php
            echo $key === 0 ? $thumbnail : $img[1];
            if (!empty($link)):
                ?>
            </a></dt>
        <?php endif; ?>
        <dd<?php if ($settings['gallery_image_title'] === 'yes' && $title !== ''): ?> class="wp-caption-text gallery-caption"<?php endif; ?>>
            <?php if ($settings['gallery_image_title'] === 'yes' && !empty($title)): ?>
                <strong class="themify_image_title"><?php echo $title; ?></strong>
            <?php endif; ?>
            <?php if ($settings['gallery_exclude_caption'] !== 'yes' && !empty($caption)): ?>
                <span class="themify_image_caption"><?php echo $caption; ?></span>
            <?php endif ?>
        </dd>
    </dl>

<?php endforeach; // end loop  ?>
