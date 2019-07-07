<?php
/**
 * Template for Main Sidebar
 * @package themify
 * @since 1.0.0
 */
?>
<?php if(!post_password_required()):?>
    <?php themify_sidebar_before(); // hook ?>

    <aside id="sidebar" itemscope="itemscope" itemtype="https://schema.org/WPSidebar">

            <?php themify_sidebar_start(); // hook ?>

            <?php dynamic_sidebar( themify_get_sidebar() ); ?>

            <?php themify_sidebar_end(); // hook ?>

    </aside>
    <!-- /#sidebar -->

    <?php themify_sidebar_after(); // hook ?>
<?php endif;?>