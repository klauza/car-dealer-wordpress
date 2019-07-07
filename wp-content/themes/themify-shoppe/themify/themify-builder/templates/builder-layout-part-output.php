<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $bids;
if(Themify_Builder::$frontedit_active || !isset($bids[$builder_id])){
    global $ThemifyBuilder, $post;
    if ( is_object( $post ) ) {
            $saved_post = clone $post;
    }
    $bids[$builder_id] = 1;
    $post = get_post( $builder_id );
    $styles = $ThemifyBuilder->stylesheet->test_and_enqueue( true, $post->ID );
    if ( $styles ) {
        $fonts = $ThemifyBuilder->stylesheet->enqueue_fonts( array() );
        ?>
        <link class="themify-builder-generated-css" type="text/css" rel="stylesheet" href="<?php echo $styles['url']?>" />
        <?php if ( ! empty( $fonts ) ) : ?>
                <link class="themify-builder-generated-css" type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=<?php echo implode( '|', $fonts ); ?>" />
        <?php endif;
    }
    if ( isset( $saved_post )  ) {
        $post = $saved_post;
    }
}
Themify_Builder::$frontedit_active = false;
?>
<div class="themify_builder_content themify_builder_content-<?php echo $builder_id; ?> themify_builder not_editable_builder" data-postid="<?php echo $builder_id; ?>">
    <?php
    foreach ($builder_output as $rows => $row) :
        if (!empty($row)) {
            if (!isset($row['row_order'])) {
                $row['row_order'] = $rows; // Fix issue with import content has same row_order number
            }
            echo Themify_Builder_Component_Row::template($rows, $row, $builder_id, false, false);
        }
    endforeach; // end row loop
    ?>
</div>