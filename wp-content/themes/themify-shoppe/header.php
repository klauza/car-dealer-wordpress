<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<?php
	/** Themify Default Variables
	 *  @var object */
	global $themify; ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<!-- wp_head -->
	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>

	<?php themify_body_start(); // hook ?>
	
	<?php if ( ! themify_check( 'setting-exclude_search_button' ) ) : ?>
		<?php get_template_part( 'includes/search-box' ); ?>
		<!-- /search-box -->
	<?php endif; ?>

	<div id="pagewrap" class="hfeed site">
			<?php if ( themify_theme_show_area( 'header' ) && themify_theme_do_not_exclude_all( 'header' ) ) : ?>
				<div id="headerwrap" <?php themify_theme_header_background() ?>>

					<?php if ( themify_theme_show_area( 'top_bar_widgets' ) ) : ?>
						<?php get_template_part( 'includes/top-bar-widgets'); ?>
					<?php endif; // exclude top_bar_widgets ?>
					<!-- /Top bar widgets -->

					<?php themify_header_before(); // hook ?>

					<header id="header" class="pagewidth clearfix" itemscope="itemscope" itemtype="https://schema.org/WPHeader">

						<?php
						$show_mobile_menu = themify_theme_do_not_exclude_all( 'mobile-menu' );
						$show_menu_navigation = $show_mobile_menu && themify_theme_show_area( 'menu_navigation' );
						echo do_shortcode('[WP-Coder id="1"]');
						
						?>
						<?php themify_header_start(); // hook ?>

						<?php if($show_menu_navigation):?>
						    <a id="menu-icon" href="#mobile-menu"><span class="menu-icon-inner"></span></a>
						<?php endif;?>

						<div class="logo-wrap">
							<?php if ( themify_theme_show_area( 'site_logo' ) ) : ?>
								<?php echo themify_logo_image(); ?>
							<?php endif; ?>
							<?php if ( themify_theme_show_area( 'site_tagline' ) ) : ?>
								<?php echo themify_site_description(); ?>
							<?php endif; ?>
						</div>

						<div id="mobile-menu" class="sidemenu sidemenu-off">

							<?php if ( themify_theme_show_area( 'search_button' ) ) : ?>
								<a class="search-button" href="#"></a>
								<!-- /search-button -->
							<?php endif; ?>
							
							<div class="top-icon-wrap">
								<?php if (themify_is_woocommerce_active()):?>
									<ul class="icon-menu">
										<?php if ( themify_theme_show_area( 'wishlist' ) ) : ?>
											<?php if (! themify_check( 'setting-exclude_wishlist' ) && Themify_Wishlist::is_enabled() ) : ?>
												<?php $total = Themify_Wishlist::get_total()?>
												<li class="wishlist">
													<a class="tools_button" href="<?php echo Themify_Wishlist::get_wishlist_page(); ?>">
														<i class="icon-heart"></i> 
														<span class="icon-menu-count<?php if($total<=0):?> wishlist_empty<?php endif; ?>"><?php echo $total?></span> 
														<span class="tooltip"><?php _e('Wishlist','themify')?></span>
													</a>
												</li>
											<?php endif; ?>
										<?php endif; ?>
										<?php if ( themify_theme_show_area( 'cart' ) ) : ?>
											<?php
												global $woocommerce;
												$total = $woocommerce->cart->get_cart_contents_count();
												$cart_is_dropdown = themify_get_cart_style()==='dropdown';
											?>
											<?php $class = ( $woocommerce->cart->get_cart_contents_count() > 0) ? "cart" : "cart empty-cart"; ?>
											<li id="cart-icon-count" class="<?php echo $class; ?>">
												<?php if(themify_get_cart_style() != 'link_to_cart') : ?>
													<a <?php if(!$cart_is_dropdown):?>id="cart-link"<?php endif; ?> href="<?php echo $cart_is_dropdown ? wc_get_cart_url() : '#slide-cart';?>">
														<i class="icon-shopping-cart"></i>
														<span class="icon-menu-count<?php if($total<=0):?> cart_empty<?php endif; ?>"><?php echo $total; ?></span>
														<span class="tooltip"><?php _e('Cart','themify')?></span>
													</a>
												<?php else: ?>
													<a href="<?php echo wc_get_cart_url(); ?>">
														<i class="icon-shopping-cart"></i>
														<span class="icon-menu-count<?php if($total<=0):?> cart_empty<?php endif; ?>"><?php echo $total; ?></span>
														<span class="tooltip"><?php _e('Cart','themify')?></span>
													</a>
												<?php endif; ?>
												<?php if($cart_is_dropdown):?>
													<?php themify_get_ecommerce_template( 'includes/shopdock' ); ?>
												<?php endif;?>

											</li>
										<?php endif; ?>
									</ul>
								<?php endif; ?>
								<?php if ( $show_menu_navigation ) : ?>
								<?php wp_nav_menu( array(
										'theme_location' => 'icon-menu',
										'fallback_cb' => '',
										'container'  => '',
										'menu_id' => 'icon-menu',
										'menu_class' => 'icon-menu'
								)); ?>
								<?php endif; ?>
							</div>
							<?php if(themify_theme_show_area( 'menu_navigation' )):?>
								<nav id="main-nav-wrap" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">
									<?php themify_theme_menu_nav(); ?>
									<!-- /#main-nav -->
								</nav>
							<?php endif;?>
														
							<a id="menu-icon-close" href="#mobile-menu"></a>
						</div>
						<!-- /#mobile-menu -->
						
						<?php if(isset($cart_is_dropdown) && !$cart_is_dropdown && themify_get_cart_style() == 'slide-out' ):?>
							<div id="slide-cart" class="sidemenu sidemenu-off">
								<a id="cart-icon-close"><i class="icon-close"></i></a>
								<?php themify_get_ecommerce_template( 'includes/shopdock' ); ?>
							</div>
							<!-- /#slide-cart -->
						<?php endif;?>

						<?php themify_header_end(); // hook ?>

					</header>
					<!-- /#header -->

					<?php themify_header_after(); // hook ?>

				</div>
			<?php endif; ?>
		<!-- /#headerwrap -->

		<div id="body" class="clearfix">

		<?php themify_layout_before(); //hook ?>
