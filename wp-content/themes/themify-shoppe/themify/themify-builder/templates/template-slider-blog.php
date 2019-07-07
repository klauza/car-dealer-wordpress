<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Slider Blog
 *
 * Access original fields: $settings
 * @author Themify
 */
$type = $settings['layout_display_slider'];
$fields_default = array(
    'post_type' => 'post',
    'taxonomy' => 'category',
    $type . '_category_slider' => '',
    'posts_per_page_slider' => '',
    'offset_slider' => '',
    'order_slider' => 'desc',
    'orderby_slider' => 'date',
    'display_slider' => 'content',
    'hide_post_title_slider' => 'no',
    'hide_feat_img_slider' => 'no'
);
if (isset($settings[$type . '_category_slider'])) {
    $settings[$type . '_category_slider'] = self::get_param_value($settings[$type . '_category_slider']);
}
$fields_args = wp_parse_args($settings, $fields_default);
unset($settings);
if ($type !== 'blog') {
    $fields_args['post_type'] = $type;
    $fields_args['taxonomy'] = $type . '-category';
}
$terms = $fields_args[$type . '_category_slider'];
$temp_terms = explode(',', $terms);
$new_terms = $new_exclude_terms = array();
$is_string = false;
foreach ($temp_terms as $t) {
    $is_numeric = is_numeric($t);
    if (!$is_numeric) {
        $is_string = true;
    }
    if ('' !== $t) {
        $result_array = ( ( $is_numeric && 0 <= $t ) || $is_string ) ? 'new_terms' : 'new_exclude_terms';
		array_push( $$result_array, is_numeric( $t ) ? abs( trim( $t ) ) : trim( $t ) );
    }
}
$tax_field = ( $is_string ) ? 'slug' : 'id';

// The Query
$args = array(
    'post_type' => $fields_args['post_type'],
    'post_status' => 'publish',
    'order' => $fields_args['order_slider'],
    'orderby' => $fields_args['orderby_slider'],
    'suppress_filters' => false
);

if ($fields_args['posts_per_page_slider'] !== '') {
    $args['posts_per_page'] = $fields_args['posts_per_page_slider'];
}

// tax query
if (!empty($new_terms) && !in_array('0', $new_terms)) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => $fields_args['taxonomy'],
            'field' => $tax_field,
            'terms' => $new_terms
        )
    );
}
if (!empty($new_exclude_terms)) {
    $args['tax_query'][] = array(
        'taxonomy' => $fields_args['taxonomy'],
        'field' => $tax_field,
        'terms' => $new_exclude_terms,
        'operator' => 'NOT IN',
    );
}

// add offset posts
if ($fields_args['offset_slider'] !== '') {
    $args['offset'] = $fields_args['offset_slider'];
}
$is_img_disabled = Themify_Builder_Model::is_img_php_disabled();
$args = apply_filters('themify_builder_slider_' . $type . '_query_args', $args);
global $post;
$temp_post = $post;
$posts = get_posts($args);
if (!empty($posts)):
    $param_image = 'w=' . $fields_args['img_w_slider'] . '&h=' . $fields_args['img_h_slider'] . '&ignore=true';
    $attr_link_target = 'yes' === $fields_args['open_link_new_tab_slider'] ? ' target="_blank" rel="noopener"' : '';
    if ($is_img_disabled && $fields_args['image_size_slider'] !== '') {
        $param_image .= '&image_size=' . $fields_args['image_size_slider'];
    }
    foreach ($posts as $post): setup_postdata($post);
        ?>
        <li>
            <div class="slide-inner-wrap"<?php if ($fields_args['margin'] !== ''): ?> style="<?php echo $fields_args['margin']; ?>"<?php endif; ?>>
                <?php
                if (($ext_link = themify_builder_get('external_link'))) {
                    $ext_link_type = 'external';
                } elseif (($ext_link = themify_builder_get('lightbox_link'))) {
                    $ext_link_type = 'lightbox';
                } else {
                    $ext_link = themify_get_featured_image_link();
                    $ext_link_type = false;
                }
                if ($fields_args['hide_feat_img_slider'] !== 'yes') {

                    // Check if there is a video url in the custom field
                    if (($vurl = themify_builder_get('video_url'))) {
                        global $wp_embed;

                        $post_image = $wp_embed->run_shortcode('[embed]' . esc_url($vurl) . '[/embed]');
                    } else {
                        $post_image = themify_get_image($param_image);
                    }
                    if ($post_image) {
                        ?>
                        <?php themify_before_post_image(); // Hook ?>
                        <figure class="slide-image">
                            <?php if ($fields_args['unlink_feat_img_slider'] === 'yes'): ?>
                                <?php echo $post_image; ?>
                            <?php else: ?>
                                <a href="<?php echo $ext_link; ?>"
                                   <?php if ('lightbox' !== $ext_link_type && 'yes' === $fields_args['open_link_new_tab_slider']): ?> target="_blank" rel="noopener"<?php endif; ?>
                                   <?php if ('lightbox' === $ext_link_type) : ?> class="themify_lightbox" rel="prettyPhoto[slider]"<?php endif; ?>>
                                   <?php echo $post_image; ?>
                                </a>
                            <?php endif; ?>
                        </figure>
                        <?php themify_after_post_image(); // Hook ?>
                    <?php } ?>
                <?php } ?>

                <?php if ($fields_args['hide_post_title_slider'] !== 'yes' || $fields_args['display_slider'] !== 'none'): ?>
                    <div class="slide-content">
                        <?php if ($fields_args['hide_post_title_slider'] !== 'yes'): ?>
                            <?php if ($fields_args['unlink_post_title_slider'] === 'yes'): ?>
                                <h3 class="slide-title"><?php the_title(); ?></h3>
                            <?php else: ?>
                                <h3 class="slide-title">
                                    <a href="<?php echo $ext_link; ?>"  
                                       <?php if ('lightbox' !== $ext_link_type && 'yes' === $fields_args['open_link_new_tab_slider']): ?> target="_blank" rel="noopener"<?php endif; ?>
                                       <?php if ('lightbox' === $ext_link_type) : ?> class="themify_lightbox" rel="prettyPhoto[slider]"<?php endif; ?>>
                                       <?php the_title(); ?>
                                    </a>
                                </h3>
                            <?php endif; //unlink post title     ?>
                        <?php endif; // hide post title  ?>
                        <?php
                        // fix the issue more link doesn't output
                        global $more;
                        $more = 0;
                        if ($fields_args['display_slider'] === 'content') {
                            the_content();
                        } elseif ($fields_args['display_slider'] === 'excerpt') {
                            the_excerpt();
                        }
                        ?>
                        <?php if ($type === 'testimonial'): ?>
                            <p class="testimonial-author">
                                <?php
                                echo themify_builder_testimonial_author_name($post, 'yes');
                                ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <!-- /slide-content -->
                <?php endif; ?>
            </div>
        </li>
        <?php
    endforeach;
    wp_reset_postdata();
    $post = $temp_post;
    ?>
<?php endif; ?>
<!-- /themify_builder_slider -->