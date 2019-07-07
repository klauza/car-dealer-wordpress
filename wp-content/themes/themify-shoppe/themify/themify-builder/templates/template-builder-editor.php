<!DOCTYPE html>
<html <?php language_attributes(); ?>>

    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        
        <!-- wp_header -->
        <?php wp_head(); ?>
    </head>
<?php if (Themify_Builder_Model::is_front_builder_activate()) : ?>
    <body class="single single-template-builder-editor themify_builder_active builder-breakpoint-desktop">
<?php else: ?>
	<body class="single single-template-builder-editor builder-breakpoint-desktop">
<?php endif; ?>
        <div class="single-template-builder-container">

            <?php if (have_posts()) while (have_posts()) : the_post(); ?>
                <?php if( get_post_type( get_the_ID() ) === 'tbuilder_layout_part') show_admin_bar( false ); ?>
                    <h2 class="builder_title"><?php the_title() ?></h2>
                    <?php the_content(); ?>
            <?php endwhile; ?>

        </div>
        <!-- /.single-template-builder-container -->

        <!-- wp_footer -->
        <?php wp_footer(); ?>

    </body>

</html>