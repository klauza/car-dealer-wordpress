<?php
/**
 * Themify admin page
 *
 * @package Themify
 */

if( file_exists( THEMIFY_DIR . '/promotion/themify-promotion.php' ) ) {
	include( THEMIFY_DIR . '/promotion/themify-promotion.php' );
}

///////////////////////////////////////////
// Create Nav Options
///////////////////////////////////////////
function themify_admin_nav() {
	$theme = wp_get_theme();
	/**
	 * Add Themify menu entry
	 * @since 2.0.2
	 */
	add_menu_page( 'themify', $theme->display('Name') , 'manage_options', 'themify', 'themify_page', get_template_directory_uri().'/themify/img/favicon.png', '49.3' );
	/**
	 * Add Themify settings page
	 * @since 2.0.2
	 */
	add_submenu_page( 'themify', $theme->display('Name'), __('Themify Settings', 'themify'), 'manage_options', 'themify', 'themify_page' );
	if ( Themify_Builder_Model::builder_check() ) {
		/**
		 * Add Themify Builder Layouts page
		 * @since 2.0.2
		 */
		add_submenu_page ( 'themify', __( 'Builder Layouts', 'themify' ), __( 'Builder Layouts', 'themify' ), 'edit_posts', 'edit.php?post_type=tbuilder_layout' );
		/**
		 * Add Themify Builder Layout Parts page
		 * @since 2.0.2
		 */
		add_submenu_page( 'themify', __( 'Builder Layout Parts', 'themify' ), __( 'Builder Layout Parts', 'themify' ), 'edit_posts', 'edit.php?post_type=tbuilder_layout_part' );
	}
	/**
	 * Add Themify Customize submenu entry
	 * @since 2.0.2
	 */
	add_submenu_page( 'themify', 'themify_customize', __( 'Customize', 'themify' ), 'manage_options', 'customize.php?themify=1' );
	/**
	 * Add submenu entry that redirects to Themify documentation site
	 * @since 2.0.2
	 */
	add_submenu_page( 'themify', $theme->display('Name'), __('Documentation', 'themify'), 'manage_options', 'themify_docs', 'themify_docs' );
}

function themify_get_theme_required_plugins() {
	$info = get_file_data( trailingslashit( get_template_directory() ) . 'style.css', array( 'Required Plugins' ) );
	if( isset( $info[0] ) ) {
		return $info[0];
	}

	return false;
}

/*  Pages
/***************************************************************************/

///////////////////////////////////////////
// Themify Documentation
///////////////////////////////////////////
function themify_docs() {
	$theme = wp_get_theme();
	$doc_path = str_replace( 'themify-', '', $theme->get_template() );
	?>
	<script type="text/javascript">window.location = "https://themify.me/docs/<?php echo $doc_path; ?>-documentation";</script>
	<?php
}

///////////////////////////////////////////
// Themify Page
///////////////////////////////////////////
function themify_page() {

	if ( ! current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to update this site.', 'themify' ) );

	if (isset($_GET['action'])) {
		$action = 'upgrade';
		themify_updater();
	}

	$themify_config = themify_load_config();

	// check theme information
	$theme = wp_get_theme();
        $is_child = is_child_theme();
	$check_theme_name = $is_child? $theme->parent()->Name : $theme->display('Name');
	$check_theme_version = $is_child ? $theme->parent()->Version : $theme->display('Version');

	$themify_has_styling_data = themify_has_styling_data();

	/**
	 * Markup for Themify skins. It's empty if there are no skins
	 * @since 2.1.8
	 * @var string
	 */
	$themify_skins = themify_get_skins();

	/* special admin tab that shows available skins with option to import demo separately for each */
	$skins_and_demos = apply_filters( 'themify_show_skins_and_demos_admin', false );

	/** whether the theme has sample data to import */
	$sample_data = file_exists( THEME_DIR . '/sample/import.zip' );
	?>
	<!-- alerts -->
	<div class="alert"></div>
	<!-- /alerts -->

	<!-- prompts -->
	<div class="prompt-box">
		<div class="show-login">
			<form id="themify_update_form" method="post" action="admin.php?page=themify&action=upgrade&type=theme&login=true&themeversion=latest">
			<p class="prompt-msg"><?php _e('Enter your Themify login info to upgrade', 'themify'); ?></p>
			<p><label><?php _e('Username', 'themify'); ?></label> <input type="text" name="username" class="username" value=""/></p>
			<p><label><?php _e('Password', 'themify'); ?></label> <input type="password" name="password" class="password" value=""/></p>
			<input type="hidden" value="theme" name="type" />
			<input type="hidden" value="true" name="login" />
			<p class="pushlabel"><input name="login" type="submit" value="Login" class="button upgrade-login" /></p>
			</form>
		</div>
		<div class="show-error">
			<p class="error-msg"><?php _e('There were some errors updating the theme', 'themify'); ?></p>
		</div>
	</div>
	<div class="overlay">&nbsp;</div>
	<!-- /prompts -->

	<!-- html -->
	<form id="themify" method="post" action="" enctype="multipart/form-data">
	<p id="theme-title"><?php echo esc_html( $check_theme_name ); ?> <em><?php echo esc_html( $check_theme_version ); ?> (<a href="<?php echo themify_https_esc( 'https://themify.me/changelogs/' ); ?><?php echo get_template(); ?>.txt" class="themify_changelogs" target="_blank" data-changelog="<?php echo themify_https_esc( 'https://themify.me/changelogs/' ); ?><?php echo get_template(); ?>.txt"><?php _e('changelogs', 'themify'); ?></a>)</em></p>
	<p class="top-save-btn">
		<a href="#" class="save-button"><?php _e('Save', 'themify'); ?></a>
	</p>
	<div id="content">

		<!-- nav -->
		<ul id="maintabnav">
			<li class="setting"><a href="#setting"><?php _e( 'Settings', 'themify' ); ?></a></li>
			<?php if ( $themify_has_styling_data ) : ?>
				<li class="styling"><a href="#styling"><?php _e( 'Styling', 'themify' ); ?></a></li>
			<?php endif; // $themify_has_styling_data ?>
			<?php if( $skins_and_demos ) : ?>
				<li class="skins"><a href="#skins"><?php _e( 'Skins & Demos', 'themify' ); ?></a></li>
                        <?php elseif ( ! empty( $themify_skins ) ) : ?>
				<li class="skins"><a href="#skins"><?php _e( 'Skins', 'themify' ); ?></a></li>
			<?php endif; ?>
			
			<li class="transfer"><a href="#transfer"><?php _e( 'Transfer', 'themify' ); ?></a></li>
			<?php if( $sample_data && ! $skins_and_demos ) : ?>
				<li class="demo-import"><a href="#demo-import"><?php _e( 'Demo Import', 'themify' ); ?></a></li>
			<?php endif;?>
			<?php if ( themify_allow_update() ) : ?>
				<li class="update-check"><a href="#update-check"><?php _e( 'Update', 'themify' ); ?></a></li>
			<?php endif; ?>
		</ul>
		<!-- /nav -->

		<!------------------------------------------------------------------------------------>

		<!--setting tab -->
		<div id="setting" class="maintab">

			<ul class="subtabnav">
                <div class="search-setting-holder">
                    <label for="search-setting" class="search-icon ti-search"></label>
                    <input id="search-setting" type="text" class="search-setting" name="search-setting">
                    <span class="ti-close clear-search"></span>
                </div>
				<?php
				$x = true;
				foreach($themify_config['panel']['settings']['tab'] as $tab):?>
                                        <?php if ( isset( $tab['id'] )):?>
                                            <li<?php if( $x===true):?> class="selected"<?php $x = false;?><?php endif;?>><a href="<?php esc_attr_e( '#setting-' . themify_scrub_func( $tab['id'] ) )?>"><?php echo $tab['title']?></a></li>
                                        <?php endif;?>
				<?php endforeach;?>
			</ul>

			<?php $themify_settings_notice = false; ?>
			 <?php foreach($themify_config['panel']['settings']['tab'] as $tab){ ?>
				<!-- subtab: setting-<?php echo themify_scrub_func($tab['id']); ?> -->
				<div id="<?php echo esc_attr( 'setting-' . themify_scrub_func( $tab['id'] ) ); ?>" class="subtab">
					<?php
					if ( ! $themify_settings_notice ) :
						?>
						<div class="themify-info-link"><?php printf( __( 'For more info about the options below, refer to the <a href="%s">General Settings</a> documentation.', 'themify' ), 'https://themify.me/docs/general-settings' ); ?></div>
						<?php
						$themify_settings_notice = true;
					endif; // themify settings notice
					?>
					<?php
					if(is_array($tab['custom-module'])){
						if(isset($tab['custom-module']['title'],$tab['custom-module']['function']) ){
							echo themify_fieldset( $tab['custom-module']['title'], $tab['custom-module']['function'], $tab['custom-module'] );
						} else {
							foreach($tab['custom-module'] as $module){
								echo themify_fieldset( $module['title'], $module['function'],$module );
							}
						}
					}
					?>
				</div>
				<!-- /subtab: setting-<?php echo themify_scrub_func($tab['id']); ?> -->
			<?php } ?>

		</div>
		<!--/setting tab -->

		<!------------------------------------------------------------------------------------>

		<?php if ( $themify_has_styling_data ) : ?>
			<?php if( ! isset( $themify_config['panel']['styling'] ) ) : ?>
				<div id="styling" class="maintab">
					<div id="<?php echo esc_attr( 'styling-' . themify_scrub_func( $tab['id'] ) ); ?>" class="subtab">
						<div id="styling" class="maintab">
							<div class="themify-info-link"><?php printf(__('The old Styling panel has discontinued. Please use Appearance > <a href="%s">Customize</a>','themify'),  admin_url('customize.php'))?></div>
						</div>
					</div>
				</div>
			<?php else : ?>
				<!--styling tab -->
				<div id="styling" class="maintab">
			
					<?php if ( get_option( 'themify_customize_notice', 1 ) ) : ?>
						<div class="themify-big-notice black js-customize-notice">
							<h3><?php _e( 'New Customize Panel', 'themify' ); ?></h3>
							<p><strong><?php _e( 'We have a new Customize panel which allows you to customize the theme
							with live preview on the frontend. This Themify Styling panel still works as is,
							but we recommend you to start using the new Customize panel.', 'themify' ); ?></strong></p>
							<p><?php _e( 'Because the Customize panel stores data differently,
							the data in the Themify Styling is not migrated to the Customize panel. You can either start
							fresh by resetting the Themify Styling or use both as you like.',
									'themify' ); ?></p>
							<a href="#" class="button notice-dismiss" data-notice="customize"><?php _e( 'Start Customize', 'themify' ); ?></a>
							<a href="#" class="close notice-dismiss" data-notice="customize">
								<i class="ti-close"></i>
							</a>
						</div>
					<?php endif; ?>
			
					<ul class="subtabnav">
						<?php
						$x = 1;
						if(isset($themify_config['panel']['styling']['tab']['title'])){
							echo '<li class="selected"><a href="' . esc_attr( '#styling-' . themify_scrub_func( $themify_config['panel']['styling']['tab']['title'] ) ) . '">' . $themify_config['panel']['styling']['tab']['title'] . '</a></li>';
						} else {
							foreach($themify_config['panel']['styling']['tab'] as $tab){
								if($x){
									echo '<li class="selected"><a href="' . esc_attr( '#styling-' . themify_scrub_func( $tab['id'] ) ) . '">' . $tab['title'] . '</a></li>';
									$x = 0;
								} else {
									echo '<li><a href="' . esc_attr( '#styling-' . themify_scrub_func( $tab['id'] ) ) . '">' . $tab['title'] . '</a></li>';
								}
							}
						}
						?>
					</ul>
			
					<?php
					if(isset($themify_config['panel']['styling']['tab']['title'])){
					?>
						<!-- subtab: styling-<?php echo themify_scrub_func($themify_config['panel']['styling']['tab']['_a']['title']); ?> -->
						<div id="<?php echo esc_attr( 'styling-' . themify_scrub_func( $themify_config['panel']['styling']['tab']['title'] ) ); ?>" class="subtab">
								<?php
								if(is_array($themify_config['panel']['styling']['tab']['element'])){
									if(isset($themify_config['panel']['styling']['tab']['element']['title']) && isset($themify_config['panel']['styling']['tab']['element']['selector'])){
										echo themify_container(themify_scrub_func($tab['id']), $themify_config['panel']['styling']['tab']['element']);
									} else {
										foreach($themify_config['panel']['styling']['tab']['element'] as $element){
											echo themify_container(themify_scrub_func($themify_config['panel']['styling']['tab']['title']), $element);
										}
									}
								}
								?>
							</div>
							<!-- /subtab: styling-<?php echo themify_scrub_func($tab['_a']['title']); ?> -->
					<?php
					} else {
						foreach($themify_config['panel']['styling']['tab'] as $tab){ ?>
							<!-- subtab: styling-<?php echo themify_scrub_func($tab['id']); ?> -->
							<div id="<?php echo esc_attr( 'styling-' . themify_scrub_func( $tab['id'] ) ); ?>" class="subtab">
								<?php
								if(is_array($tab['element'])){
									if(isset($tab['element']['title']) && isset($tab['element']['selector'])){
										echo themify_container(themify_scrub_func($tab['id']), $tab['element']);
									} else {
										foreach($tab['element'] as $element){
											echo themify_container(themify_scrub_func($tab['id']), $element);
										}
									}
								}
								?>
							</div>
							<!-- /subtab: styling-<?php echo themify_scrub_func($tab['id']); ?> -->
						<?php }
					}
					?>
			
				</div>
				<!--/styling tab -->
			<?php endif; ?>
		<?php endif; // $themify_has_styling_data ?>

		<!------------------------------------------------------------------------------------>

		<!--skins tab -->
		<?php
		if ( ! empty( $themify_skins ) ) : ?>
			<div id="skins" class="maintab">
				<ul class="subtabnav">
					<li class="selected"><a href="#setting-general"><?php _e('Skins', 'themify'); ?></a></li>
				</ul>

				<div id="load-load" class="subtab">
					<?php if( $skins_and_demos ) : ?>
						<div class="themify-info-link"><?php _e( 'Select a skin & import the demo content (demo import is optional). Import demo will import the content (posts/pages), Themify panel settings, menus and widgets as our demo. Erase demo will delete only the imported posts/pages (Themify panel settings, widgets, existing and modified imported posts/pages will not be affected).', 'themify' ); ?></div>
					<?php endif; ?>
					<div class="themify-skins">
						<input type="hidden" name="skin" value="<?php echo esc_url( themify_get( 'skin' ) ); ?>">
						<?php echo themify_get_skins_admin(); ?>
					</div>
				</div>
			</div>
			<!--/skins tab -->
		<?php endif; ?>

		<!------------------------------------------------------------------------------------>

		<!--transfer tab -->
		<div id="transfer" class="maintab">
			<ul class="subtabnav">
				<li><a href="#transfer-import"><?php _e( 'Theme Settings', 'themify' ); ?></a></li>
			</ul>

			<div id="transfer-import" class="subtab">
				<div class="themify-info-link"><?php _e( 'Click "Export" to export the Themify panel data which you can use to import in the future by clicking the "Import" button. Note: this will only export/import the data within the Themify panel (the WordPress settings, widgets, content, comments, page/post settings, etc. are not included).', 'themify' ) ?></div>

				<div class="biggest-transfer-btn">
				<input type="hidden" id="import" />
				 <?php themify_uploader( 'import', array(
							'label' => __('Import', 'themify'),
							'confirm' => __('Import will overwrite all settings and configurations. Press OK to continue, Cancel to stop.', 'themify') )
						); ?>

				<em><?php _e('or', 'themify'); ?></em>
				<?php
				/**
				 * URL of Themify Settings Page properly nonced.
				 * @var String
				 */
				$baseurl = wp_nonce_url(admin_url('admin.php?page=themify'), 'themify_export_nonce');
				$baseurl = add_query_arg( 'export', 'themify', $baseurl );
				?>
				<a href="<?php echo esc_url( $baseurl ) ?>" class="export" id="download-export"><?php _e('Export', 'themify'); ?></a>
				</div>
			</div>

		</div>
		<!--/transfer tab -->

		<?php if( $sample_data && ! $skins_and_demos ) : ?>
		<!--demo import tab -->
		<div id="demo-import" class="maintab">
			<ul class="subtabnav">
				<li><a href="#demo-import"><?php _e( 'Demo Import', 'themify' ); ?></a></li>
			</ul>

			<div id="demo-import" class="subtab demo-import-main">
				<p>
				<a href="#" class="button import-sample-content" data-default="<?php _e( 'Import Demo', 'themify' ); ?>" data-success="<?php _e( 'Done', 'themify' ); ?>" data-importing="<?php _e( 'Importing', 'themify' ) ?>"> <i class="ti-arrow-down"></i> <span><?php _e( 'Import Demo', 'themify' ); ?></span> </a>
				</p>
				<p><?php _e( 'Import Demo will import the content (posts/pages), Themify panel settings, menus and widgets as our demo. Due to copyright reasons, demo images will be replaced with a placeholder image.', 'themify' ); ?></p>
				<p>
				<a href="#" class="button erase-sample-content" data-default="<?php _e( 'Erase Demo', 'themify' ); ?>" data-erasing="<?php _e( 'Erasing', 'themify' ); ?>" data-success="<?php _e( 'Done', 'themify' ); ?>"> <i class="ti-close"></i> <span><?php _e( 'Erase Demo', 'themify' ); ?></span> </a>
				</p>
				<p><?php _e( 'Erase demo will delete the imported posts/pages. Existing and modified imported post/page will not be deleted. Themify panel settings and widgets will not be removed. You may import the content again later.', 'themify' ); ?></p>
			</div>

		</div>
		<!--/demo import tab -->
		<?php endif; ?>

		<?php if ( themify_allow_update() ) : ?>
		<!--update theme/framework tab -->
		<div id="update-check" class="maintab">
			<ul class="subtabnav">
				<li><a href="#update-main"><?php _e( 'Update', 'themify' ); ?></a></li>
			</ul>

			<div id="update-main" class="subtab update-main">
				<?php
				ob_start();
				themify_check_version( 'tab' );
				$update_message = ob_get_contents();
				ob_end_clean();
				$button_label = __( 'Check for Updates', 'themify' );
				$update_available = __( 'Check for theme and framework updates.', 'themify' );
				if ( isset( $_GET['update'] ) && 'check' == $_GET['update'] ) {
					$button_label = __( 'Check Again', 'themify' );
					$update_available = __( 'No updates available.', 'themify' );
				}

				if ( $update_message ) : ?>
					<?php if ( false !== strpos( $update_message, 'reinstalltheme' ) && false === strpos( $update_message, 'updateready' ) ) : ?>
						<p><a href="<?php echo esc_url( add_query_arg( 'update', 'check#update-check', admin_url('admin.php?page=themify') ) ); ?>" class="button big-button update"><span><?php echo esc_html( $button_label ); ?></span></a>
						</p>
						<p><?php echo esc_html( $update_available ); ?></p>
					<?php endif; ?>
					<?php echo !empty( $update_message ) ? $update_message : ''; ?>
				<?php else : ?>
					<p><a href="<?php echo esc_url( add_query_arg( 'update', 'check#update-check', admin_url('admin.php?page=themify') ) ); ?>" class="button big-button update"><span><?php echo esc_html( $button_label ); ?></span></a>
					</p>
					<p><?php echo esc_html( $update_available ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<!--/update theme/framework tab -->
		<?php endif; // user can update_themes ?>

		<!------------------------------------------------------------------------------------>

	</div>
	<!--/content -->

	<?php if( get_option( get_template() . '_themify_import_notice', 1 ) ) : ?>
		<div id="demo-import-notice" class="themify-modal">
		<?php if( $skins_and_demos ) : ?>
			<p><?php _e( 'Skins & Demos', 'themify' ); ?></p>
			<p><?php _e( 'Select a skin and import the demo content as per our demo (optional). You can do this later at the Skins & Demos tab.', 'themify' ); ?></p>
			<div class="skins-demo-import-notice">
				<?php echo themify_get_skins_admin(); ?>
			</div>
		<?php else : ?>
			<h3><?php _e( 'Import Demo', 'themify' ); ?></h3>
			<p><?php _e( 'Would you like to import the demo content to have the exact look as our demo?', 'themify' ); ?></p>
			<p><?php _e( 'You may import or erase demo content later at the Import tab of the Themify panel.', 'themify' ); ?></p>
			<a href="#" class="button import-sample-content" data-default="<?php _e( 'Import Demo', 'themify' ); ?>" data-success="<?php _e( 'Done', 'themify' ); ?>" data-importing="<?php _e( 'Importing', 'themify' ) ?>"> <i class="ti-arrow-down"></i> <span><?php _e( 'Yes, import', 'themify' ); ?></span> </a>
			<a href="#" class="thanks-button dismiss-import-notice"> <?php _e( 'No, thanks', 'themify' ); ?> </a>
		<?php endif; ?>
			<a href="#" class="close dismiss-import-notice"><i class="ti-close"></i></a>
		</div>
		<?php
			// disable the demo import modal after first visit
			update_option( get_template() . '_themify_import_notice', 0 ); ?>
	<?php endif; ?>

		<?php
		$required_plugins = themify_get_theme_required_plugins();
		if( ! empty( $required_plugins ) ) {
			echo themify_required_plugins_modal( $required_plugins );
		}
		?>

	<!-- footer -->
	<div id="bottomtab">
	   <p id="logo"><a href="<?php echo themify_https_esc( 'https://themify.me/logs/framework-changelogs/' ); ?>" data-changelog="<?php echo themify_https_esc( 'https://themify.me/changelogs/themify.txt' ); ?>" target="_blank" class="themify_changelogs">v<?php echo THEMIFY_VERSION; ?></a></p>
		<div class="reset">
			<strong><?php _e( 'Reset', 'themify' ); ?></strong>
			<ul>
				<li><a href="#" id="reset-setting" class="reset-button"><?php _e('Settings', 'themify'); ?></a></li>
				<li><?php if ( $themify_has_styling_data ) : ?>
					<a href="#" id="reset-styling" class="reset-button"><?php _e('Styling', 'themify'); ?></a>
				<?php endif; ?></li>
			</ul>
		</div>
		<p class="btm-save-btn">
			<a href="#" class="save-button"><?php _e('Save', 'themify'); ?></a>
		</p>
	</div>
	<!--/footer -->

	</form>
        <script type="text/javascript">
	/**
	 * Ensure checkboxes are included in the data sent to server
	 * Fixes checkboxes not being saved
	 */
	jQuery( function($){
		$('#themify :checkbox').each(function(){
			$( this ).before( '<input type="hidden" name="' + $( this ).attr( 'name' ) + '" value="" />' );
		});
	});
	</script>
	<div class="clearBoth"></div>
	<!-- /html -->

	<?php
	do_action('themify_settings_panel_end');
}

/**
 * Return an array of available theme skins
 *
 * @since 2.7.8
 * @return array
 */
function themify_get_skins(){
	// Open Styles Folder
	$dir = trailingslashit( get_template_directory() ) . '/skins';

	$skins = array( array(
		'name' => __( 'No Skin', 'themify' ),
		'version' => null,
		'description' => null,
		'screenshot' => get_template_directory_uri() . '/themify/img/non-skin.gif',
		'has_demo' => false,
	) );
	if ( is_dir( $dir ) ) {
		if( $handle = opendir( $dir ) ){
			// Grab Folders
			while ( false !== ( $dirTwo = readdir($handle) ) ) {
				if( $dirTwo !== '.' && $dirTwo !== '..' ) {
					$path = trailingslashit( $dir ) . $dirTwo;
					if( is_file( $path . '/style.css' ) ) {
						$info = get_file_data( $path . '/style.css', array( 'Skin Name', 'Version', 'Description', 'Demo URI', 'Required Plugins' ) );
						$skins[$dirTwo] = array(
							'name' => $info[0],
							'version' => $info[1],
							'description' => $info[2],
							'screenshot' => is_file( $path . '/screenshot.jpg' ) ? get_template_directory_uri().'/skins/'. $dirTwo . '/screenshot.jpg'
											: ( is_file( $path . '/screenshot.png' ) ? get_template_directory_uri().'/skins/'. $dirTwo . '/screenshot.png'
											: get_template_directory_uri() . '/themify/img/screenshot-na.png' ),
							'has_demo' => is_file( $path . '/import.zip' ),
							'demo_uri' => $info[3],
							'required_plugins' => $info[4]
						);
					}
				}
			}
			closedir($handle);
		}
	}
	ksort( $skins );

	return apply_filters( 'themify_theme_skins', $skins );
}

/**
 * Display the admin field for the theme skins
 *
 * @return string
 */
function themify_get_skins_admin(){
	$data = themify_get_data();
	$skins = themify_get_skins();
	$output = '';

	if( ! empty( $skins ) ) {
		foreach( $skins as $id => $skin ) {
			$selected = trailingslashit( get_template_directory_uri() ) . "skins/{$id}/style.css" == themify_get( 'skin' ) ? 'selected' : '';

			if( $id === 'default' &&! themify_check( 'skin' ) ) {
				$selected = 'selected';
			}

			$output .= '
				<div class="skin-preview '. $selected .'" data-skin="'. $id .'">
				<a href="#"><img src="' . esc_url( $skin['screenshot'] ) . '" alt="' . esc_attr__( 'Skin', 'themify' ) . '" id="' . esc_attr( trailingslashit( get_template_directory_uri() ) . "skins/{$id}/style.css" ) . '" /></a>
				<br />' . $skin['name'];
			if(! empty( $skin['demo_uri'] ) ) {
				$output .= sprintf( ' <span class="view-demo"><a href="%s" target="_blank">%s</a></span>', $skin['demo_uri'], __( 'view demo', 'themify' ) );
			}
			if( $skin['has_demo'] ) {
				$output .= '<div class="skin-demo-content" data-skin="' . esc_attr( $id ) . '">';
					$output .= __( 'Demo:', 'themify' );
					$output .= ' <a href="#" class="skin-demo-import">' . __( 'Import', 'themify' ) . '</a> <a href="#" class="skin-erase-demo">' . __( 'Erase', 'themify' ) . '</a>';
				$output .= '</div>';
				$required_plugins = $skin['required_plugins'];
				$output .= themify_required_plugins_modal( $required_plugins, $id );
			}

			$output .= '</div>';
		}
	}

	return $output;
}

function themify_required_plugins_modal( $required_plugins, $skin = '' ) {
	$output = '';
	if( ! empty( $required_plugins ) ) {
		$required_plugins = array_map( 'trim', explode( ',', $required_plugins ) );
		$required_plugins = array_map( 'themify_get_known_plugin_info', $required_plugins );
	}
	if( ! empty( $required_plugins ) && ! themify_are_plugins_active( wp_list_pluck( $required_plugins, 'path' ) ) ) {
		$all_plugins = get_plugins();
		$output .= '<div class="required-addons themify-modal" style="display: none;" data-skin="' . $skin . '">';
			$output .= '<p>' . __( 'This demo requires these plugins/addons:', 'themify' ) . '</p>';
			$output .= '<ul>';
			foreach( $required_plugins as $plugin ) {
				$state = isset( $all_plugins[$plugin['path']] ) ? is_plugin_active( $plugin['path'] ) ? __( '<span class="ti-check"></span>', 'themify' ) : __( 'installed, but not active', 'themify' ) : __( 'not installed', 'themify' );
				$output .= '<li>' . sprintf( "<a href='%s' class='external-link'>%s</a> (%s)", $plugin['page'], $plugin['name'], $state ) . '</li>';
			}
			$output .= '</ul>';
			$output .= '<p class="themify-import-warning">' . __( 'Proceed import without the required addons/plugins might show incomplete/missing content.', 'themify' ) . '</p>';
			$output .= '<p>' . sprintf( __( 'If you have an active Themify membership, download the missing addons from the <a href="https://themify.me/member" target="_blank">Member Area</a>. Then install and activate them at <a href="%s" class="external-link">WP Admin > Plugins</a>.', 'themify' ), admin_url( 'plugins.php' ) ) . '</p>';
			$output .= '<a href="#" class="proceed-import button big-button">' . __( 'Proceed Import', 'themify' ) . '</a>';
			$output .= '<a href="#" class="close dismiss-import-notice"><i class="ti-close"></i></a>';
		$output .= '</div>';
	}
	return $output;
}

/**
 * Create Settings Fieldset
 *
 * @param string $title
 * @param string $module
 * @param string $attr
 *
 * @return string
 */
function themify_fieldset( $title = '', $module = '', $attr = '' ) {
	$data = themify_get_data();
	$data_param =  isset( $data['setting'][$title] )? $data['setting'][$title] : '';
	if( is_array( $module ) && is_callable( $module ) ) {
		$function = $module;
	} else {
		$function = '';
		$module = trim( $module );
		$module = themify_scrub_func( $module );
		if ( function_exists( 'themify_' . $module ) ) {
			$function = 'themify_' . $module;
		} else if ( function_exists( $module ) ) {
			$function = $module;
		}
		if ( '' == $function ) {
			return '';
		}
	}
	$tmp_id = is_string( $function ) ? 'id="'. esc_attr( $function ) .'"' : '' ;
	$output = '<fieldset '.$tmp_id.'><legend><span>' . esc_html( $title ) . '</span><i class="ti-plus"></i></legend><div class="themify_panel_fieldset_wrap">';
	$output .= call_user_func( $function, array(
		'data' => $data_param,
		'attr' => $attr )
	);
	$output .= '</div></fieldset>';
	return $output;
}

/**
 * Create styles container
 */
function themify_container( $category = '', $element = array() ) {
	$data = themify_get_data();
	$temp = array();
	if(is_array($data)){
		$new_arr = array();
		foreach($data as $name => $value){
			$array = explode('-',$name);
			$path = "";
			foreach($array as $part){
				$path .= "[$part]";
			}
			$new_arr[ $path ] = $value;
		}
		$temp = themify_convert_brackets_string_to_arrays( $new_arr );
	}
	if($element){
		$base_id = $element['id'];
		$output = '	<fieldset><legend>' . esc_html( $element['title'] ) . '</legend>';
		if(is_array($element['module'])){
			foreach($element['module'] as $module){
				$module_name = themify_is_associative_array($module)?$module['name']:$module;

				$title = $element['id'];
				$attr = $module;
				$module = trim(str_replace(array(' ','-','|'),array('','_','hr'),$module_name));
				$value = isset( $temp['styling'][$category][$title][$module] )? $temp['styling'][$category][$title][$module] : '';
				if(function_exists('themify_'.$module)){
					$output .=	call_user_func("themify_".$module, array('category' => $category, 'title' => $title, 'value' => $value, 'attr' => $attr, 'id' => $base_id));
				} elseif(function_exists($module)){
                                    $output .=	call_user_func($module, array('category' => $category, 'title' => $title, 'value' => $value, 'attr' => $attr, 'id' => $base_id));
				}
			}
		}
		$output .= '</fieldset>';
		return $output;
	}
}

/**
 * Get details about a known plugin
 *
 * @param $name if omitted, returns the entire list
 * @since 2.8.6
 */
function themify_get_known_plugin_info( $name = '' ) {
	$plugins = array(
		'builder-ab-image'          => array(
			'name' => __( 'Builder A/B Image', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/ab-image.jpg',
			'desc' => 'Compare 2 images side by side',
			'page' => 'https://themify.me/addons/ab-image',
			'path' => 'builder-ab-image/init.php',
		),
		'builder-audio'             => array(
			'name' => __( 'Builder Audio', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/audio.jpg',
			'desc' => 'Elegant audio playlist',
			'page' => 'https://themify.me/addons/audio',
			'path' => 'builder-audio/init.php'
		),
		'builder-bar-chart'         => array(
			'name' => __( 'Builder Bar Chart', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/bar-chart.jpg',
			'desc' => '',
			'page' => 'https://themify.me/addons/bar-chart',
			'path' => 'builder-bar-chart/init.php'
		),
		'builder-button'            => array(
			'name' => __( 'Builder Button Pro', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/button.jpg',
			'desc' => 'Custom designed action buttons',
			'page' => 'https://themify.me/addons/button',
			'path' => 'builder-button/init.php'
		),
		'builder-contact'           => array(
			'name' => __( 'Builder Contact', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/contact.jpg',
			'desc' => 'Simple contact form',
			'page' => 'https://themify.me/addons/contact',
			'path' => 'builder-contact/init.php'
		),
		'builder-countdown'         => array(
			'name' => __( 'Builder Countdown', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/countdown.jpg',
			'desc' => 'Count down events and promotions',
			'page' => 'https://themify.me/addons/countdown',
			'path' => 'builder-countdown/init.php'
		),
		'builder-counter'           => array(
			'name' => __( 'Builder Counter', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/counter.jpg',
			'desc' => 'Animated circles and number counters',
			'page' => 'https://themify.me/addons/counter',
			'path' => 'builder-counter/init.php'
		),
		'builder-fittext'           => array(
			'name' => __( 'Builder FitText', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/fittext.jpg',
			'desc' => 'Auto fit text in the container',
			'page' => 'https://themify.me/addons/fittext',
			'path' => 'builder-fittext/init.php'
		),
		'builder-image-pro'         => array(
			'name' => __( 'Builder Image Pro', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/image-pro.jpg',
			'desc' => 'Beautify images with image filters, color/image overlay, and animation effects',
			'page' => 'https://themify.me/addons/image-pro',
			'path' => 'builder-image-pro/init.php'
		),
		'builder-infinite-posts'    => array(
			'name' => __( 'Builder Infinite Posts', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/infinite-posts.jpg',
			'desc' => 'Display posts in infinite scrolling on parallax, grid, overlay, or list view',
			'page' => 'https://themify.me/addons/infinite-posts',
			'path' => 'builder-infinite-posts/init.php'
		),
		'builder-bar-chart'        => array(
			'name' => __( 'Builder Bat Chart', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/bar-chart.jpg',
			'desc' => 'Display bar graphs',
			'page' => 'https://themify.me/addons/bar-chart',
			'path' => 'builder-bar-chart/init.php'
		),
		'builder-maps-pro'          => array(
			'name' => __( 'Builder Maps Pro', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/maps-pro.jpg',
			'desc' => 'Multiple markers, custom icons, tooltips, and 40+ map styles',
			'page' => 'https://themify.me/addons/maps-pro',
			'path' => 'builder-maps-pro/init.php'
		),
		'builder-pie-chart'         => array(
			'name' => __( 'Builder Pie Chart', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/pie-chart.jpg',
			'desc' => '',
			'page' => 'https://themify.me/addons/pie-chart',
			'path' => 'builder-pie-chart/init.php'
		),
		'builder-pointers'          => array(
			'name' => __( 'Builder Pointers', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/pointers.jpg',
			'desc' => 'Highlight certain areas of your image',
			'page' => 'https://themify.me/addons/pointers',
			'path' => 'builder-pointers/init.php'
		),
		'builder-pricing-table'     => array(
			'name' => __( 'Builder Pricing Table', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/pricing-table.jpg',
			'desc' => 'Beautiful and responsive pricing table addon',
			'page' => 'https://themify.me/addons/pricing-table',
			'path' => 'builder-pricing-table/init.php'
		),
		'builder-progress-bar'      => array(
			'name' => __( 'Builder Progress Bar', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/progress-bar.jpg',
			'desc' => 'Animated bars based on input percentage',
			'page' => 'https://themify.me/addons/progress-bar',
			'path' => 'builder-progress-bar/init.php'
		),
		'builder-slider-pro'        => array(
			'name' => __( 'Builder Slider Pro', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/slider-pro.jpg',
			'desc' => 'Make stunning sliders with transition and animation effects',
			'page' => 'https://themify.me/addons/slider-pro',
			'path' => 'builder-slider-pro/init.php'
		),
		'builder-tiles'             => array(
			'name' => __( 'Builder Tiles', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/tiles.jpg',
			'desc' => 'Drag & drop tiles to create Windows 8 Metro layouts',
			'page' => 'https://themify.me/addons/tiles',
			'path' => 'builder-tiles/init.php'
		),
		'builder-timeline'          => array(
			'name' => __( 'Builder Timeline', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/timeline.jpg',
			'desc' => 'Display content in a timeline-styled layouts',
			'page' => 'https://themify.me/addons/timeline',
			'path' => 'builder-timeline/init.php'
		),
		'builder-typewriter'        => array(
			'name' => __( 'Builder Typewriter', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/typewriter.jpg',
			'desc' => 'Display your text with eye-catching typing animation',
			'page' => 'https://themify.me/addons/typewriter',
			'path' => 'builder-typewriter/init.php'
		),
		'builder-woocommerce'       => array(
			'name' => __( 'Builder WooCommerce', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/woocommerce.jpg',
			'desc' => 'Show WooCommerce products anywhere in the Builder',
			'page' => 'https://themify.me/addons/woocommerce',
			'path' => 'builder-woocommerce/init.php'
		),
		'contact-form-7'            => array(
			'name' => __( 'Contact Form 7', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/ab-image.jpg',
			'desc' => '',
			'page' => 'https://wordpress.org/plugins/contact-form-7/',
			'path' => 'contact-form-7/wp-contact-form-7.php'
		),
		'themify-portfolio-post'    => array(
			'name' => __( 'Portfolio Posts', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/ab-image.jpg',
			'desc' => '',
			'page' => 'https://themify.me',
			'path' => 'themify-portfolio-post/themify-portfolio-post.php'
		),
		'mailchimp-for-wp'          => array(
			'name' => __( 'MailChimp for WordPress', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/ab-image.jpg',
			'desc' => '',
			'page' => 'https://wordpress.org/plugins/mailchimp-for-wp/',
			'path' => 'mailchimp-for-wp/mailchimp-for-wp.php'
		),
		'woocommerce'               => array(
			'name' => __( 'WooCommerce', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/ab-image.jpg',
			'desc' => '',
			'page' => 'https://wordpress.org/plugins/woocommerce/',
			'path' => 'woocommerce/woocommerce.php'
		),
		'themify-wc-product-filter' => array(
			'name' => __( 'Themify Product Filter', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/ab-image.jpg',
			'desc' => '',
			'page' => 'https://themify.me/themify-product-filter',
			'path' => 'themify-wc-product-filter/themify-wc-product-filter.php'
		),
		'themify-shortcodes' => array(
			'name' => __( 'Themify Shortcodes', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/themify-shortcodes.jpg',
			'desc' => '',
			'page' => 'https://wordpress.org/plugins/themify-shortcodes/',
			'path' => 'themify-shortcodes/init.php'
		),
		'themify-event-post' => array(
			'name' => __( 'Themify Event Post', 'themify' ),
			'image' => 'https://themify.me/wp-content/product-img/addons/themify-shortcodes.jpg',
			'desc' => '',
			'page' => 'https://wordpress.org/plugins/themify-event-post/',
			'path' => 'themify-event-post/themify-event-post.php'
		),
		'learnpress' => array(
			'name' => __( 'LearnPress', 'themify' ),
			'image' => 'https://ps.w.org/learnpress/assets/icon-256x256.png',
			'desc' => '',
			'page' => 'https://wordpress.org/plugins/learnpress/',
			'path' => 'learnpress/learnpress.php'
		),
	);

	if( empty( $name ) ) {
		return $plugins;
	} elseif( isset( $plugins[$name] ) ) {
		return $plugins[$name];
	}
}

/**
 * Themify Admin Widgets
 */

if( !function_exists( 'themify_add_admin_widgets' ) ):
function themify_add_admin_widgets() {
	wp_add_dashboard_widget( 'themify_news', esc_html__( 'Themify News', 'themify' ), 'themify_news_admin_widget' );
	if( current_user_can( 'install_themes' ) ) {
		wp_add_dashboard_widget( 'themify_updates', esc_html__( 'Themify Updates', 'themify' ), 'themify_updates_admin_widget' );
	}
}
endif;
add_action( 'wp_dashboard_setup', 'themify_add_admin_widgets' );

// Admin Widgets Ajax
function themify_admin_widgets_ajax() {
	require_once ABSPATH . 'wp-admin/includes/dashboard.php';

	$pagenow = $_GET['pagenow'];
	if ( $pagenow === 'dashboard-user' || $pagenow === 'dashboard-network' || $pagenow === 'dashboard' ) {
		set_current_screen( $pagenow );
	}

	switch ( $_GET['widget'] ) {
		case 'themify_news' :
			themify_news_admin_widget();
			break;
	}
	wp_die();
}
add_action( 'wp_ajax_themify_admin_widgets', 'themify_admin_widgets_ajax', 1 );

// Themify News Admin Widget
function themify_news_admin_widget() {
	$feeds = array(
		'news' => array(
			'link'			=> 'https://themify.me/blog',
			'url'			=> 'https://themify.me/blog/feed',
			'title'			=> esc_html__( 'Themify News', 'themify' ),
			'items'			=> 4,
			'show_summary'	=> 1,
			'show_author'	=> 0,
			'show_date'		=> 1
		)
	);

	wp_dashboard_cached_rss_widget( 'themify_news', 'wp_dashboard_primary_output', $feeds );
}

function themify_check_update_link( $plugin, $type ) {
	global $admin_page_hooks;
	if( !empty($admin_page_hooks[$plugin]) && $type === 'plugin' && strpos( 'builder-' , $plugin) === false ) {
		return esc_url( admin_url( 'admin.php?page=' . $plugin ) );
	}

	return esc_url( admin_url( 'admin.php?page=themify#update-check' ) );
}

// Themify News Admin Widget
function themify_updates_admin_widget() {
	$cached_data = get_transient( 'themify_widget_current_updates' );

	if( empty( $cached_data ) ) {
		$versions_url = 'https://themify.me/versions/versions.xml';
		$response = wp_remote_get( $versions_url, array( 'sslverify' => false ) );
		if( is_wp_error( $response ) ) {
			return;
		}

		if( !empty( $response['body'] ) ) {
			$versions = themify_xml2array( $response['body'] );
			$current_theme = wp_get_theme();
			$current_theme_name = is_child_theme() ? $current_theme->parent()->Name : $current_theme->get( 'Name' );
			$current_theme_version = is_child_theme() ? $current_theme->parent()->Version : $current_theme->get( 'Version' );
			$current_theme_version = intval( str_replace( '.', '', trim( $current_theme_version ) ) );
			$installed_plugins = get_plugins();
			$update_items = '';
			$update_theme = '';
			$update_link = network_admin_url('update-core.php');
			foreach( $versions['versions']['_c']['version'] as $update ) {
				$latest_version = intval( str_replace( '.', '', trim( $update['_v'] ) ) );
				if( strpos( $update['_a']['name'], strtolower( str_replace( ' ', '-', $current_theme_name ) ) ) !== false 
					&& $current_theme_version < $latest_version ) {
					$update_theme = sprintf( '<li class="themify-update-theme">
							<div class="themify-theme-thumb"><img src="%s"/></div>
							<div class="themify-theme-meta">
								<h2>%s<span>V. %s</span></h2>
								<p>%s</p>
								<a href="%s" class="themify-update-button">%s</a>
								<a href="%s" target="_blank">%s</a>
							</div>	
						</li>'
						, $current_theme->get_screenshot()
						, $current_theme_name
						, trim( $update['_v'] )
						, $current_theme->get( 'Description' ) 
						, esc_url( $update_link )
						, esc_html__( 'Update now', 'themify' )
						, esc_url( '//themify.me/changelogs/' . $update['_a']['name'] . '.txt' )
						, esc_html__( 'Changelog', 'themify' ) );
				}

				if( !empty( $installed_plugins ) ) {
					foreach( $installed_plugins as $key => $plugin ) {
						if( strpos( $update['_a']['name'], dirname( $key ) ) !== false 
							&& intval( str_replace( '.', '', trim( $plugin['Version'] ) ) ) < $latest_version ) {
								$update_url = $update_link;
								$button_class = 'themify-update-button';

							if ( isset($update['_a']['free']) ) {
								if( ! function_exists( 'install_plugin_install_status' ) ) {
									include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
								}

								$plugin_info = install_plugin_install_status( (object) array(
									'slug' => $update['_a']['name'],
									'version' => $latest_version
								) );

								if( ! empty( $plugin_info ) && $plugin_info['status'] === 'update_available' ) {
									$update_url = $plugin_info['url'];
									$button_class .= ' themify-update-ajax';
								}

								$update_items .= sprintf( '<li class="themify-update-plugins">
									<h2>%s<span>V. %s</span></h2>
									<a href="%s" target="_blank">%s</a>
									<a href="%s" target="_blank" class="%s">%s</a>
								</li>'
								, $plugin['Name']
								, preg_replace( '/\B/', '.', $latest_version )
								, esc_url( '//themify.me/changelogs/' . $update['_a']['name'] . '.txt' )
								, esc_html__( 'Changelog', 'themify' ) 
								, esc_url( $update_url )
								, esc_attr( $button_class )
								, esc_html__( 'Update', 'themify' ) );
							} else {
								$update_items .= sprintf( '<li class="themify-update-plugins">
									<h2>%s<span>V. %s</span></h2>
									<a href="%s" target="_blank">%s</a>
									<a href="%s" target="_blank" class="themify-update-button">%s</a>
								</li>'
								, $plugin['Name']
								, preg_replace( '/\B/', '.', $latest_version )
								, esc_url( '//themify.me/changelogs/' . $update['_a']['name'] . '.txt' )
								, esc_html__( 'Changelog', 'themify' ) 
								, esc_url( $update_link )
								, esc_html__( 'Update', 'themify' ) );
							}
						}
					}
				}
			}

			$content = '';

			if( !empty( $update_theme ) || !empty( $update_items ) ) {
				$content = sprintf( '<ul>%s</ul>', $update_theme . $update_items );
			} else {
				$content = __( 'No updates available.', 'themify' );
			}

			echo $content;
			set_transient( 'themify_widget_current_updates', $content, HOUR_IN_SECONDS );
		}
	} else {
		printf( '<ul>%s</ul>', $cached_data );
	}

}
function themify_admin_widget_delete_transient() {
	check_admin_referer( 'ajax-admin-widget-nonce', 'nonce' );
	delete_transient( 'themify_widget_current_updates' );
	wp_die();
}
add_action( 'wp_ajax_themify_admin_widget_delete_transient', 'themify_admin_widget_delete_transient', 1 );

///////////////////////////////////////////
// Site Logo
///////////////////////////////////////////
function themify_site_logo( $data = array() ) {
	if($data['attr']['target'] != ''){
		$target = "<span class='hide target'>" . esc_html( $data['attr']['target'] ) . "</span>";
	} else {
		$target = '';
	}
	$data = themify_get_data();
	$text = '';
	$image = '';
	if( isset( $data['setting-site_logo'] ) && 'image' === $data['setting-site_logo'] ) {
		$image = "checked='checked'";
		$image_display = "style='display:block;'";
	} else {
		$text = "checked='checked'";	
		$image_display = "style='display:none;'";
	}
	$logo_image_value = themify_get( 'setting-site_logo_image_value' );
	$logo_image_width = themify_get( 'setting-site_logo_width' );
	$logo_image_height = themify_get( 'setting-site_logo_height' );
	return '<div class="themify_field_row">
				<span class="label">'. __('Display', 'themify') .'</span> 
					<input name="setting-site_logo" type="radio" value="text" '.$text.' /> ' . __('Site Title', 'themify') . '
					<input name="setting-site_logo" type="radio" value="image" '.$image.' /> ' . __('Image', 'themify') . '
				</span>
				'.$target.'
				<div class="uploader-fields image" '.$image_display.'>
					<input type="text" id="setting-site_logo" class="width10" name="setting-site_logo_image_value" value="' . esc_url( $logo_image_value ) . '" />
					<div class="clear image" '.$image_display.'>' . themify_get_uploader('setting-site_logo', array('tomedia' => true)) . '</div>
				</div>
				<span class="pushlabel clear image" '.$image_display.'>
					<input type="text" name="setting-site_logo_width" class="width2" value="' . esc_attr( $logo_image_width ) . '" /> ' . __('width', 'themify') . '
					<input type="text" name="setting-site_logo_height" class="width2" value="' . esc_attr( $logo_image_height ) . '" /> ' . __('height', 'themify') . '
				</span>
			</div>';
}

///////////////////////////////////////////
// Favicon Module
///////////////////////////////////////////
function themify_favicon( $data = array() ) {
	if($data['attr']['target'] != ''){
		$target = "<span class='hide target'>".$data['attr']['target']."</span>";	
	} else {
		$target = '';
	}
	$setting_favicon = themify_get( 'setting-favicon' );
	return '<div class="themify_field_row">
				<span class="label">'. __('Custom Favicon', 'themify') . '</span>
				<input id="setting-favicon" type="text" class="width10" name="setting-favicon" value="' . esc_attr( $setting_favicon ) . '" /> <br />
				'.$target.'
				<span class="pushlabel" style="display:block;">
					' . themify_get_uploader('setting-favicon', array('tomedia' => true)) . '
				</span>
			</div>';
}

///////////////////////////////////////////
// Favicon Module - Action
///////////////////////////////////////////
add_action('admin_head', 'themify_favicon_action');

///////////////////////////////////////////
// Default Layouts
///////////////////////////////////////////
if (!function_exists('themify_custom_post_type_layouts')) :
/**
 * Default Custom Post sidebar Module
 * @param array $data Theme settings data
 * @return string Markup for module.
 * @since 4.0.0
 */
function themify_custom_post_type_layouts($data = array()){
	$data = themify_get_data();

	/**
	 * Theme Settings Option Key Prefix
	 * @var string
	 */
	$prefix = 'setting-custom_post_';

	/**
	 * Module markup
	 * @var string
	*/

	$output = '';

	$custom_posts = null;

	$post_types = get_post_types(array('public' => true, 'publicly_queryable' => 'true'), 'objects');
	$excluded_types = apply_filters( 'themify_exclude_CPT_for_sidebar', array('post', 'page', 'attachment', 'tbuilder_layout', 'tbuilder_layout_part', 'section'));


	foreach ($post_types as $key => $value) {
		if (!in_array($key, $excluded_types)) {
			$custom_posts[$key] =  array( 'name' => $value->labels->singular_name, 'archive' => $value->has_archive );
		}
	}

	$custom_posts = apply_filters('themify_get_public_post_types', $custom_posts);

	/**
	 * Sidebar placement options
	 * @var array
	 */
	$sidebar_location_options = apply_filters('themify_post_type_theme_sidebars' , array(
									array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
									array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
									array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar ', 'themify'))
								), false );
	/**
	 * Page sidebar placement
	 */
	
	if(is_array($custom_posts)){
		foreach($custom_posts as $key => $cPost){
			$output .= '<p>
							'. sprintf('<h4>%s %s</h4>', strtoupper($cPost['name']), __('POST TYPE', 'themify'));
			
			if ($cPost['archive']) {

				$output .= '<p>'. sprintf('<span class="label">%s %s</span>', ucfirst($cPost['name']), __('Archive Sidebar', 'themify'));
				$val = isset( $data[$prefix.$key.'_archive'] ) ? $data[$prefix.$key.'_archive'] : '';

				foreach ( $sidebar_location_options as $option ) {
					if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
						$val = $option['value'];
					}
					if ( $val == $option['value'] ) {
						$class = "selected";
					} else {
						$class = "";
					}
					$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
				}

				$output .= '<input type="hidden" name="'.($prefix.$key).'_archive" class="val" value="'.$val.'" /></p>';
			}
			
			$output .= '<p>'. sprintf('<span class="label">%s %s</span>', ucfirst($cPost['name']), __('Single Sidebar', 'themify'));
			$val = isset( $data[$prefix.$key.'_single'] ) ? $data[$prefix.$key.'_single'] : '';

			foreach ( $sidebar_location_options as $option ) {
				if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
					$val = $option['value'];
				}
				if ( $val == $option['value'] ) {
					$class = "selected";
				} else {
					$class = "";
				}
				$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
			}
			$output .= '<input type="hidden" name="'.($prefix.$key).'_single" class="val" value="'.$val.'" /></p>
						</p>';
		}
	}

	return $output;
}

endif;

///////////////////////////////////////////
// Custom Feed URL Module
///////////////////////////////////////////
function themify_custom_feed_url( $data = array() ) {
	$custom_feed_url = themify_get( 'setting-custom_feed_url' );
	return '<p><span class="label">' . __( 'Custom Feed URL', 'themify' ) . '</span> <input type="text" class="width10" name="setting-custom_feed_url" value="' . esc_attr( $custom_feed_url ) . '" /> <br />
			<span class="pushlabel"><small>' . __( 'e.g. http://feedburner.com/userid', 'themify' ) . '</small></span></p>';
}

///////////////////////////////////////////
// Meta Description Module
///////////////////////////////////////////
function themify_meta_description( $data = array() ) {
	$data = themify_get_data();
	return '<p><textarea name="setting-meta_description" class="widthfull" rows="4">'.$data['setting-meta_description'].'</textarea></p>';
}

/**
 * Header HTML Module
 * @param array $data
 * @return string
 */
function themify_header_html( $data = array() ) {
	$header_html = themify_get( 'setting-header_html' );
	return '<p>' . __('The following code will add to the &lt;head&gt; tag.', 'themify') . '</p>
				<p><textarea class="widthfull" rows="10" name="setting-header_html">'.$header_html.'</textarea><br />
				<small>' . __('Useful if you need to add additional scripts such as CSS or JS.', 'themify') . '</small></p>';	
}


/**
 * Footer HTML Module
 * @param array $data
 * @return string
 */
function themify_footer_html( $data = array() ) {
	$footer_html = themify_get( 'setting-footer_html' );
	return '<p>' . __('The following code will be added to the footer before the closing &lt;/body&gt; tag.', 'themify') . '</p>
				<p><textarea type="text" class="widthfull" rows="10" name="setting-footer_html">'.$footer_html.'</textarea><br />
				<small>' . __('Useful if you need to Javascript or tracking code.', 'themify') . '</small></p>';
}

/**
 * Custom CSS Module
 * @param array $data
 * @return string
 */
function themify_custom_css( $data = array() ) {
	$custom_css = themify_get( 'setting-custom_css' );
	return '<p><textarea class="widthfull" rows="35" name="setting-custom_css">'.$custom_css.'</textarea><br /></p>';
}

///////////////////////////////////////////
// Search Settings Module
///////////////////////////////////////////
function themify_search_settings( $data = array() ) {
	$data            = themify_get_data();
	$post_checked         = '';
	$checked         = '';
	$search_settings = themify_get( 'setting-search_settings' );
	if ( themify_get( 'setting-search_settings_exclude' ) ) {
		$checked = 'checked="checked"';
	}
	if ( themify_get( 'setting-search_exclude_post' ) ) {
		$post_checked = 'checked="checked"';
	}
	$out = '<p>
				<span class="label">' . __( 'Search in Category IDs', 'themify' ) . ' </span>
				<input type="text" class="width6" name="setting-search_settings" value="' . esc_attr( $search_settings ) . '" />
			</p>
			<p>
				<span class="pushlabel"><small>' . __( 'Use minus sign (-) to exclude categories.', 'themify' ) . '</small></span><br />
				<span class="pushlabel"><small>' . __( 'Example: (1,4,-7) = search only in Category 1 &amp; 4, and exclude Category 7.', 'themify' ) . '</small></span>
			</p>
			<p>
				<span class="pushlabel"><label for="setting-search_exclude_post"><input type="checkbox" id="setting-search_exclude_post" name="setting-search_exclude_post" ' . $post_checked . '/> ' . __( 'Exclude Posts in search results', 'themify' ) . '</label></span>
			</p>
			<p>
				<span class="pushlabel"><label for="setting-search_settings_exclude"><input type="checkbox" id="setting-search_settings_exclude" name="setting-search_settings_exclude" ' . $checked . '/> ' . __( 'Exclude Pages in search results', 'themify' ) . '</label></span>
			</p>';


	$pre        = 'setting-search_exclude_';
	$checkboxes = '';

	$exclude_types = apply_filters( 'themify_types_excluded_in_search', get_post_types( array(
		'_builtin'            => false,
		'public'              => true,
		'exclude_from_search' => false
	) ) );

	foreach ( array_keys( $exclude_types ) as $post_type ) {

		$type = get_post_type_object( $post_type );

		if ( is_object( $type ) ) {
			$checkboxes .= '
		<p>
			<span class="pushlabel">
				<label for="' . $pre . $type->name . '">
					<input type="checkbox" id="' . $pre . $type->name . '" name="' . esc_attr( $pre . $type->name ) . '" ' . checked( isset( $data[ $pre . $type->name ] ) ? $data[ $pre . $type->name ] : '', 'on', false ) . '/> ' . sprintf( __( 'Exclude %s in search results', 'themify' ), $type->labels->name ) . '
				</label>
			</span>
		</p>';
		}
	}

	if ( '' != $checkboxes ) {
		$out .= $checkboxes;
	}

	return $out;
}

///////////////////////////////////////////
// 404 Page Settings Module
///////////////////////////////////////////
if( !function_exists( 'page_404_settings' ) ){
	function page_404_settings(){
		$data            = themify_get_data();
		$page_404 = themify_get( 'setting-page_404' );
		$max = 100;
		$args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'post_type' => 'page',
			'post_status' => 'publish',
			'posts_per_page' => $max
		);
		$pages = new WP_Query( $args );
					$front = get_option('page_on_front');
		$out = '<p><span class="label">' . __( 'Custom 404 Page', 'themify' ) . ' </span>';
					if($pages->max_num_pages>1){
						$post_name = '';
						if($page_404){
							$post_name = get_post($page_404);
							if(!empty($post_name)){
								$post_name = esc_attr($post_name->post_title);
							}
						}
						$out .= '<span class="themify_404_autocomplete_wrap">';
						$out .= '<input type="text" value="'.$post_name.'" id="themify_404_page_autocomplete" /><input type="hidden" name="setting-page_404" value="'.$page_404.'" />';
						$out .= '</span>';
					}
					else{
						$out.='<select name="setting-page_404"> 
						<option value="0">'.esc_attr( __( 'Select page', 'themify'  ) ).'</option>';
						while ( $pages->have_posts() ) {
														$pages->the_post();
														$id = get_the_ID();
														if($id!=$front){
							$selected         = '';
							if ( $page_404 == $id ) {
								$selected = 'selected="selected"';
							}
							$out .= '<option '.$selected.' value="' . $id . '">';
							$out .= get_the_title();
							$out .= '</option>';
														}
						}
						$out .= '</select>';
					}
		$out .= '<p><span class="pushlabel"><small>' . __('First create a new Page (eg. 404) and then select it here. The selected page will be used for error 404 (when a URL is not found on your site).', 'themify') . '</small></span></p>';
					wp_reset_postdata();
		return $out;
	}
}

///////////////////////////////////////////
// RSS Feed Settings Module
///////////////////////////////////////////
function themify_feed_settings( $data = array() ) {
	$checked_use = '';
	$feed_settings = themify_get( 'setting-feed_settings' );
	$feed_custom_post = themify_get( 'setting-feed_custom_post' );
	$custom_posts = array_diff( get_post_types( array('public' => true, 'publicly_queryable' => 'true' ) )
		, array('attachment', 'tbuilder_layout', 'tbuilder_layout_part', 'section') );
	$custom_posts_options = '<option></option>';

	if ( 'on' === themify_get( 'setting-exclude_img_rss' ) ) {
		$checked_use = 'checked="checked"';
	}

	if( ! empty( $custom_posts ) ) {
		array_unshift( $custom_posts, 'all' );
		$feed_custom_post_arr = explode( ',', trim( $feed_custom_post ) );

		foreach( $custom_posts as $c_post ) {
			$custom_posts_options .= sprintf( '<option %s value="%s">%s</option>'
			, in_array( $c_post, $feed_custom_post_arr ) ? 'selected="selected"' : ''
			, $c_post
			, ucfirst( preg_replace( "/[-_]/", ' ', $c_post ) ) );
		}
	}

	return '<p><span class="label">' . __('Feed Category', 'themify') . '</span> <input type="text" class="width6" name="setting-feed_settings" value="' . esc_attr( $feed_settings ) . '" /></p>
			<p>
				<span class="pushlabel"><small>' . __('Use minus sign (-) to exclude categories.', 'themify') . '</small></span><br />
				<span class="pushlabel"><small>' . __('Example: (2,-9) = include only Category 2 in feeds and exclude Category 9.', 'themify') . '</small></span>
			</p>
			<p><span class="label">' . __('Post Image in RSS', 'themify') . '</span> <label for="setting-exclude_img_rss"><input type="checkbox" id="setting-exclude_img_rss" name="setting-exclude_img_rss" '.$checked_use.'/> ' . __('Exclude featured image in RSS feeds', 'themify') . '</label></p>
			<p>
				<span class="pushlabel"><small>' . __('Check this to exclude post image in RSS feeds', 'themify') . '</small></span>
			</p>
			<p><span class="label">' . __('Custom Posts in RSS', 'themify') . '</span>
				<select size="6" multiple="multiple" class="width10 themify_multiselect">' . $custom_posts_options . '</select>
					<input type="hidden" name="setting-feed_custom_post" value="' . esc_attr( $feed_custom_post ) . '" />
			</p>
			<p><span class="pushlabel"><small>' . __( 'Select "All" to add all available posts in your feed or select the specific ones.', 'themify') . '</small></span><br /></p>';
}

/**
 * Outputs Image Script module in theme settings.
 */
function themify_img_settings( $data = array() ) {
	$feature_sizes = themify_get_image_sizes_list();
	$checked_use = '';
	if ( 'on' === themify_get( 'setting-img_settings_use' ) ) {
		$checked_use = "checked='checked'";
	}
	$output = '
	<div class="module">
		<div class="themify-info-link">' . sprintf( __( 'The image script is used to generate featured images dynamically in any dimension. If your images are cropped manually, disable it for faster performance. For more info about the image script, refer to the <a href="%s">Image Script</a> documentation.', 'themify' ), 'https://themify.me/docs/image-script' ) . '
		</div>
		<fieldset>
		<div class="label">' . __( 'Disable', 'themify' ) . '</div> 
		<div class="row">
			<label for="setting-img_settings_use"><input type="checkbox" id="setting-img_settings_use" name="setting-img_settings_use" class="disable_img_php" ' . $checked_use . '/> ' . __( 'Disable image script globally', 'themify' ) . '</label><br/>
			<small class="pushlabel">' . __( 'Default WordPress image sizes or original images will be used.', 'themify' ) . '</small>
			<br/>
		</div>
		<div class="show_if_enabled_img_php">
			<div class="label">' . __('Base Image Size', 'themify') . '</div>
			<div class="row">
				<select name="setting-img_php_base_size">';
					foreach ( $feature_sizes as $option ) {
						if ( $option['value'] == themify_get( 'setting-img_php_base_size', 'large' ) ) {
							$output .= '<option value="' . esc_attr( $option['value'] ) . '" selected="selected">';
							$output .= $option['name'];
							$output .= '</option>';
						} else {
							$output .= '<option value="' . esc_attr( $option['value'] ) . '">' . $option['name'] . '</option>';
						}
					}
					$output .= '
				</select>
				<small class="pushlabel">
				' . __( 'Select the image size that image script will resize thumbnails from. If you\'re not sure, leave it as "Large".', 'themify' ) . '
				</small>
			</div>
		</div>
		<div class="show_if_disabled_img_php">
			<div class="label">' . __('Default Featured Image Size', 'themify') . '</div>
			<div class="show_if_disabled_img_php row">
				<select name="setting-global_feature_size">';
					foreach ( $feature_sizes as $option ) {
						if ( $option['value'] == themify_get( 'setting-global_feature_size' ) ) {
							$output .= '<option value="' . esc_attr( $option['value'] ) . '" selected="selected">';
							$output .= $option['name'];
							$output .= '</option>';
						} else {
							$output .= '<option value="' . esc_attr( $option['value'] ) . '">' . $option['name'] . '</option>';
						}
					}
					$output .= '
				</select>
			</div>
		</div>
		</fieldset>
		
	</div>';
	return $output;
}

/* 	Styling Modules
/***************************************************************************/	

///////////////////////////////////////////
// Divider Module
///////////////////////////////////////////
function themify_divider( $data = array() ) {
	return '<hr/>';
}

///////////////////////////////////////////
// Image Preview Module
///////////////////////////////////////////
function themify_image_preview( $data = array() ) {
	global $themify_config;

	//Get currently selected preset
	$savedpath = themify_get( 'styling-'.$data['category'].'-'.$data['id'].'-background_image-value-value' );
	
	//Begin presets block
	$output = '<div class="preset">';

	// If it's activation, delete presets
	if( !empty( $_GET['firsttime'] )) {
		delete_option('themify_background_presets');
	}

	//Check to see if we have already loaded the presets
	$presets = get_option('themify_background_presets');
	if( false == $presets ){
		//if presets were not loaded, create a variable to store them
		$presets = array();

		//Presets first time
		if($data['attr']['src'] != ''){
			$img_folders = array('src' => $data['attr']['src']); 	
		} else {
			$img_folders = $themify_config['folders']['images'];
		}
		if(is_array($img_folders)){
			if(isset($img_folders['src'])){
				$folder = $img_folders['src'];
				if(is_dir(THEME_DIR.'/'.$folder)){
					if($handle = opendir(THEME_DIR.'/'.$folder)) {
						while (false !== ($file = readdir($handle))) {
							$ext = substr(strrchr($file, '.'), 1);
							if($ext === 'jpg' || $ext === 'gif' || $ext === 'png'){
								$fullpath = get_template_directory_uri().'/'.$folder.$file;
								$is_selected = trim($fullpath) === trim($savedpath)? 'selected': '';
								$output .= '<a href="#" title="'.$folder.$file.'"><span title="'.$folder.$file.'"></span>
									<img src="' . esc_url( $fullpath ) . '" alt="' . esc_attr( $fullpath ) . '" class="backgroundThumb '.$is_selected.'" /></a>';
								$presets[sanitize_file_name($file)] = $fullpath; 
							}
						}
						closedir($handle);
					}
				}
			} else {
				foreach($img_folders as $folder){
					$folder = $folder['src'];
					if(is_dir(THEME_DIR.'/'.$folder)){
						if($handle = opendir(THEME_DIR.'/'.$folder)) {
							while (false !== ($file = readdir($handle))) {
								$ext = substr(strrchr($file, '.'), 1);
								if($ext === 'jpg' || $ext === 'gif' || $ext === 'png'){
									$fullpath = get_template_directory_uri().'/'.$folder.$file;
									$is_selected = trim($fullpath) === trim($savedpath)? 'selected': '';
									$output .= '<a href="#" title="'.$folder.$file.'"><span title="'.$folder.$file.'"></span>
									<img src="' . esc_url( $fullpath ) . '" alt="' . esc_attr( $fullpath ) . '" class="backgroundThumb ' . $is_selected . '" /></a>';
									$output .= '<input type="hidden" name="preset' . sanitize_file_name($file) . '" value="' . esc_attr( $fullpath ) . '" />';
									$presets[sanitize_file_name($file)] = $fullpath;
								}
							}		
							closedir($handle);
						}
					}
				}
			}
		}
		//Presets first time END
		update_option( 'themify_background_presets', $presets );
	}
	else {
		//we have already stored our presets so go ahead and show them
		foreach ($presets as $file => $fullpath) {
			$is_selected = trim($fullpath) == trim($savedpath)? 'selected': '';
			$output .= '<a href="#" title="' . esc_attr( basename($file) ) . '"><span title="' . esc_attr( $file ) . '"></span><img src="' . esc_url( $fullpath ) . '" alt="' . esc_attr( $fullpath ) . '" class="backgroundThumb ' . $is_selected . '" /></a>';
		}			
	}
	
	//End presets block
	$output .= '</div>';		
	return $output;
}

/**
 * Refresh background presets
 * @since 1.5.1
 */
function themify_delete_background_presets() {
	delete_option('themify_background_presets');
}
add_action( 'switch_theme', 'themify_delete_background_presets' );

///////////////////////////////////////////
// Background Image Module
///////////////////////////////////////////
function themify_background_image( $data = array() ) {
	if($data['attr']['target'] != ''){
		$target = "<span class='hide target'>".$data['attr']['target']."</span>";
	} else {
		$target = '';
	}
	$data['value'] = isset( $data['value']['value'] )? $data['value']['value'] : '';
	$data_value = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$none_checked = '';
	if ( !empty( $data['value']['none'] )) {
		$none_checked = "checked='checked'";
	}
	$output = '<div class="themify_field_row background_image">
					<span class="label">' . __( 'Background Image', 'themify' ) . '</span> 
					'.$target.'
					<input type="text" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-background_image-value-value' ) . '" class="width8 upload-file" id="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-background_image' ) . '" value="' . esc_attr( $data_value ) . '" />
					<input type="checkbox" class="noBgImage" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-background_image-value-none' ) . '" '.$none_checked.' /> ' . __('No BG image', 'themify') . '<br />
					<div class="pushlabel" style="display:block;">
						'. 
		themify_get_uploader(
			'styling-'.$data['category'].'-'.$data['id'].'-background_image',
			array('preset' => true)
		)
						 . '
					</div>
				</div>';		
	return $output;
}

///////////////////////////////////////////
// Background Color Module
///////////////////////////////////////////
function themify_background_color( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] )? $data['value']['value'] : '';
	$data_value = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	if (!empty( $data['value']['transparent'] )) {
		$output = '
		<div class="themify_field_row themify_field-background_color">
			<span class="label">' . __('Background Color', 'themify') . '</span>
			<div class="themify_field-color">
				<span class="colorSelect" style=""><span></span></span> <input type="text" disabled name="' . esc_attr( 'styling-' . $data['category'] . '-' . $data['id'] . '-background_color-value-value' ) . '" class="colorSelectInput width4 opacity-7" />
				<input type="button" class="button clearColor" value="' . __('x', 'themify') . '">
				<input type="checkbox" checked="checked" name="' . esc_attr( 'styling-' . $data['category'] . '-' . $data['id'] . '-background_color-value-transparent' ) . '" class="colorTransparent" /> ' . __('Transparent', 'themify') . '
			</div>
		</div>';
	} else {
		$output = '
		<div class="themify_field_row themify_field-background_color">
			<span class="label">' . __( 'Background Color', 'themify' ) . '</span>
			<div class="themify_field-color"> 
				<span class="colorSelect " style="' . esc_attr( "background-color:#$data_value;" ) . '"><span></span></span>
				<input type="text" name="' . esc_attr( 'styling-' . $data['category'] . '-' . $data['id'] . '-background_color-value-value' ) . '" value="' . esc_attr( $data_value ) . '" class="colorSelectInput width4" />
				<input type="button" class="button clearColor" value="' . esc_attr__( 'x', 'themify' ) . '">
				<input type="checkbox" name="' . esc_attr( 'styling-' . $data['category'] . '-' . $data['id'] . '-background_color-value-transparent' ) . '" class="colorTransparent" /> ' . __( 'Transparent', 'themify' ) . '
			</div>
		</div>';
	}
	return $output;
}

///////////////////////////////////////////
// Background Repeat Module
///////////////////////////////////////////
function themify_background_repeat( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] )? $data['value']['value'] : '';
	$options = array(
			array(
				'value' => 'repeat',
				'name' => __('Repeat', 'themify')
			),
			array(
				'value' => 'repeat-x',
				'name' => __('Repeat X', 'themify')
			),
			array(
				'value' => 'repeat-y',
				'name' => __('Repeat Y', 'themify')
			),
			array(
				'value' => 'no-repeat',
				'name' => __('Do not repeat', 'themify')
			)
		);
	$output = '<p><span class="label">' . __('Background Repeat', 'themify') . '</span>
				<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-background_repeat-value-value' ) . '"><option> </option>';
	foreach($options as $option){
		$output .= '<option value="' . esc_attr( $option['value'] ) . '" ' . selected( $option['value'], isset( $data['value']['value'] ) ? $data['value']['value'] : '', false ) . '>' . esc_html( $option['name'] ) . '</option>';
	}
	$output .=	'</select></p>';	
	return $output;
}

///////////////////////////////////////////
// Background Position Module
///////////////////////////////////////////
function themify_background_position( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] )? $data['value']['value'] : '';
	$data_value_x = isset( $data['value']['x'] ) ? $data['value']['x'] : '';
	$data_value_y = isset( $data['value']['y'] ) ? $data['value']['y'] : '';
	$options = array(
			array(
				'value' => 'left',
				'name' => __('Left', 'themify')
			),
			array(
				'value' => 'center',
				'name' => __('Center', 'themify')
			),
			array(
				'value' => 'right',
				'name' => __('Right', 'themify')
			)
		);
	$output = '	<p><span class="label">' . __('Background Position', 'themify') . '</span> 
				<select class="background_position positionX"><option> </option>';
	foreach ( $options as $option ) {
		$output .= '<option value="' . esc_attr( $option['value'] ) . '" ' . selected( $option['value'], $data_value_x, false ) . '>' . esc_html( $option['name'] ) . '</option>';
	}
	$output .= '</select>
				<span class="value" style="display:none;">
					<input type="text" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-background_position-value-x' ) . '" value="' . esc_attr( $data_value_x ) . '" class="valueX">
				</span>';
	
	$options = array(
		array(
			'value' => 'top',
			'name' => __('Top', 'themify')
		),
		array(
			'value' => 'center',
			'name' => __('Center', 'themify')
		),
		array(
			'value' => 'bottom',
			'name' => __('Bottom', 'themify')
		)
	);
	$output .= '<select class="background_position positionY"><option> </option>';
	
	foreach ( $options as $option ) {
		$output .= '<option value="' . esc_attr( $option['value'] ) . '" ' . selected( $option['value'], $data_value_y, false ) . '>' . esc_html( $option['name'] ) . '</option>';
	}
	$output .= '</select>
				<span class="value" style="display:none;">
					<input type="text" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-background_position-value-y' ) . '" value="' . esc_attr( $data_value_y ) . '" class="valueY">
				</span>
				</p>';
				
	return $output;
}

///////////////////////////////////////////
// Font Family Module
///////////////////////////////////////////
function themify_font_family( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$fonts = array("Arial, Helvetica, sans-serif",
					"Verdana, Geneva, sans-serif",
					"Georgia, \"Times New Roman\", Times, serif",
					"\"Times New Roman\", Times, serif",
					"Tahoma, Geneva, sans-serif",
					"\"Trebuchet MS\", Arial, Helvetica, sans-serif",
					"Palatino, \"Palatino Linotype\", \"Book Antiqua\", serif",
					"\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif");

	$output = '<p><span class="label">' . esc_html__( 'Font Family', 'themify' ) . '</span>
				<select class="fontFamily" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-font_family-value-value' ) . '"><option> </option>';
	$output .= '<optgroup label="' . esc_attr__('Web Safe Fonts', 'themify') . '">';
	foreach ( $fonts as $font ) {
		if (isset( $data['value']['value'] ) && ( $font === $data['value']['value'] ) ) {
			$output .= '<option value=\'' . esc_attr( $font ) . '\' selected="selected">' . esc_html( $font ) . '</option>';
		} else {
			$output .= '<option value=\'' . esc_attr( $font ) . '\'>' . esc_html( $font ) . '</option>';
		}
	}
	$output .= '</optgroup>';

	$themify_gfonts = themify_get_google_font_lists();
	if ( sizeof( $themify_gfonts ) > 0 ) {
		$output .= '<optgroup label="' . esc_attr__( 'Google Fonts', 'themify' ) . '">';
		foreach ( $themify_gfonts as $font ) {
			$selected = isset( $data['value']['value'] )  && $font['family'] === $data['value']['value']  ? ' selected="selected"' : '';
			$output .= '<option value=\'' . esc_attr( $font['family'] ) . '\'' . $selected . '>' . esc_html( $font['family'] ) . '</option>';
		}
		$output .= '</optgroup>';
	}

	$output .= '</select></p>';
	return $output;
}

///////////////////////////////////////////
// Font Size Module
///////////////////////////////////////////
function themify_font_size( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$data_value = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$options = array( 'px', 'em', '%' );
	$output = '<p><span class="label">' . __( 'Font Size', 'themify' ) . '</span>
				<input type="text" class="width2 valid_num" name="' . esc_attr( 'styling-' . $data['category'] . '-' . $data['id'] . '-font_size-value-value' ) . '" value="' . esc_attr( $data_value ) . '" />
				<select name="' . esc_attr( 'styling-' . $data['category'] . '-' . $data['id'] . '-font_size-value-unit' ) . '"><option> </option>';
	foreach ( $options as $option ) {
		if ( isset( $data['value']['unit'] ) &&  $option === $data['value']['unit']  ) {
			$output .= '<option value="' . esc_attr( $option ) . '" selected="selected">' . esc_html( $option ) . '</option>';
		} else {
			$output .= '<option value="' . esc_attr( $option ) . '">' . esc_html( $option ) . '</option>';
		}
	}
	$output .= '</select></p>';

	return $output;
}

///////////////////////////////////////////
// Font Weight Module
///////////////////////////////////////////
function themify_font_weight( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$options = array(
		array(
			'value' => 'normal',
			'name'  => __( 'Normal', 'themify' )
		),
		array(
			'value' => 'bold',
			'name'  => __( 'Bold', 'themify' )
		)
	);
	$output = '<p><span class="label">' . __( 'Font Weight', 'themify' ) . '</span> <select name="' . esc_attr( 'styling-' . $data['category'] . '-' . $data['id'] . '-font_weight-value-value' ) . '"><option> </option>';
	foreach ( $options as $option ) {
		if ( isset( $data['value']['value'] ) && $option['value'] === $data['value']['value']) {
			$output .= '<option value="' . esc_attr( $option['value'] ) . '" selected="selected">' . esc_html( $option['name'] ) . '</option>';
		} else {
			$output .= '<option value="' . esc_attr( $option['value'] ) . '">' . esc_html( $option['name'] ) . '</option>';
		}
	}
	$output .= '</select></p>';
	return $output;
}

///////////////////////////////////////////
// Font Style Module
///////////////////////////////////////////
function themify_font_style( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$options = array(
		array(
			'value' => 'normal',
			'name' => __('Normal', 'themify')
		),
		array(
			'value' => 'italic',
			'name' => __('Italic', 'themify')
		)
	);
	$output = '<p><span class="label">' . __('Font Style', 'themify') . '</span> <select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-font_style-value-value' ) . '"><option> </option>';
	foreach($options as $option){
		if ( isset( $data['value']['value'] ) &&  $option['value'] === $data['value']['value'] ) {
			$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">'.esc_html( $option['name'] ).'</option>';
		} else {
			$output .= '<option value="'.esc_attr( $option['value'] ).'">'.esc_html( $option['name'] ).'</option>';
		}
	}
	$output .= '</select></p>';
	return $output;
}

///////////////////////////////////////////
// Font Variant Module
///////////////////////////////////////////
function themify_font_variant( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$options = array(
		array(
			'value' => 'normal',
			'name' => __('Normal', 'themify')
		),
		array(
			'value' => 'small-caps',
			'name' => __('Small Caps', 'themify')
		)
	);
	$output = '<p><span class="label">' . __('Font Variant', 'themify') . '</span> <select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-font_variant-value-value' ) . '"><option> </option>';
	foreach($options as $option){
		if ( isset( $data['value']['value'] ) && ( $option['value'] == $data['value']['value'] ) ) {
			$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
		} else {
			$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
		}
	}
	$output .= '</select></p>';
	return $output;
}

///////////////////////////////////////////
// Line Height Module
///////////////////////////////////////////
function themify_line_height( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$data_value = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$options = array('px','em','%');
	$output = '	<p><span class="label">' . __('Line Height', 'themify') . '</span>
				<input type="text" class="width2 valid_num"  name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-line_height-value-value' ) . '" value="' . esc_attr( $data_value ) . '" /><select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-line_height-value-unit' ) . '"><option> </option>';
	foreach($options as $option){
		if ( isset( $data['value']['unit'] ) && ( $option == $data['value']['unit'] ) ) {
			$output .= '<option value="' . esc_attr( $option ) . '" selected="selected">' . esc_html( $option ) . '</option>';
		} else {
			$output .= '<option value="' . esc_attr( $option ) . '">' . esc_html( $option ) . '</option>';
		}
	}
	$output .= '</select></p>';
	return $output;
}

///////////////////////////////////////////
// Text Transform Module
///////////////////////////////////////////
function themify_text_transform( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$options = array(
		array(
			'value' => 'capitalize',
			'name' => __('Capitalize', 'themify')
		),
		array(
			'value' => 'uppercase',
			'name' => __('Uppercase', 'themify')
		),
		array(
			'value' => 'lowercase',
			'name' => __('Lowercase', 'themify')
		),
		array(
			'value' => 'none',
			'name' => __('None', 'themify')
		)
	);
	$output = '	<p><span class="label">' . __('Text Transform', 'themify') . '</span> <select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-text_transform-value-value' ) . '"><option> </option>';
	foreach($options as $option){
		if ( isset( $data['value']['value'] ) && ( $option['value'] == $data['value']['value'] ) ) {
			$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
		} else {
			$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
		}
	}
	$output .= '</select></p>';
	return $output;
}

///////////////////////////////////////////
// Text Decoration Module
///////////////////////////////////////////
function themify_text_decoration( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$options = array(
		array(
			'value' => 'underline',
			'name' => __('Underline', 'themify')
		),
		array(
			'value' => 'overline',
			'name' => __('Overline', 'themify')
		),
		array(
			'value' => 'line-through',
			'name' => __('Line through', 'themify')
		),
		array(
			'value' => 'none',
			'name' => __('None', 'themify')
		)
	);
	$output = '	<p><span class="label">' . __('Text Decoration', 'themify') . '</span> <select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-text_decoration-value-value' ) . '"><option> </option>';
	foreach($options as $option){
		if ( isset( $data['value']['value'] ) && ( $option['value'] == $data['value']['value'] ) ) {
			$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
		} else {
			$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
		}
	}
	$output .= '</select></p>';
	return $output;
}

///////////////////////////////////////////
// Font Color Module
///////////////////////////////////////////
function themify_color( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$data_value = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	return '<div class="themify_field_row">
				<span class="label">' . __('Color', 'themify') . '</span>
				<div class="themify_field-color">
					<span class="colorSelect" style="' . esc_attr( 'background:#' . $data_value . ';' ) . '">
						<span></span>
					</span>
					<input type="text" class="colorSelectInput width4" value="' . esc_attr( $data_value ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-color-value-value' ) . '" />
					<input type="button" class="button clearColor" value="' . __('Clear', 'themify') . '">
				</div>
			</div>';
}

///////////////////////////////////////////
// Padding Module
///////////////////////////////////////////
function themify_padding( $data = array() ) {
	$individuals = '';
	$checked = '';
	$same = '';
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$data_value_same = isset( $data['value']['same'] ) ? $data['value']['same'] : '';
	$data_value_top = isset( $data['value']['top'] ) ? $data['value']['top'] : '';
	$data_value_right = isset( $data['value']['right'] ) ? $data['value']['right'] : '';
	$data_value_bottom = isset( $data['value']['bottom'] ) ? $data['value']['bottom'] : '';
	$data_value_left = isset( $data['value']['left'] ) ? $data['value']['left'] : '';
	
	if ( isset( $data['value'] ) && isset( $data['value']['checkbox'] ) && $data['value']['checkbox'] ) {
		$checked = "checked='checked'";
		$individuals = "style='display:none;'";	
	} else {
		$same = "style='display:none;'";
	}
	$output = '<div>
					<p>
						<span class="label">' . __('Padding', 'themify') . '</span>
						<span class="same" '.$same.'>
							<input type="text" class="width2 valid_num" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-padding-value-same' ) . '" value="' . esc_attr( $data_value_same ) . '" /> <small>px</small>
						</span>
						<span class="individuals" '.$individuals.'>
							<input type="text" class="width2 valid_num" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-padding-value-top' ) . '" value="' . esc_attr( $data_value_top ) . '" /> ' . __( 'top', 'themify' ) . '
							<input type="text" class="width2 valid_num" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-padding-value-right' ) . '" value="' . esc_attr( $data_value_right ) . '" /> ' . __( 'right', 'themify' ) . '
							<input type="text" class="width2 valid_num"  name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-padding-value-bottom' ) . '" value="' . esc_attr( $data_value_bottom ) . '"/> ' . __( 'bottom', 'themify' ) . '
							<input type="text" class="width2 valid_num" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-padding-value-left' ) . '" value="' . esc_attr( $data_value_left ) . '" /> ' . __( 'left', 'themify' ) . ' <small>(px)</small>
						</span>
					</p>
					<p>
						<span class="pushlabel" style="display:block;">
							<input type="checkbox" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-padding-value-checkbox' ) . '" class="padding-switch" '.$checked.' /> ' . __('Same for all', 'themify') . '
						</span>
					</p>
				</div>';
	return $output;
}

///////////////////////////////////////////
// Margin Module
///////////////////////////////////////////
function themify_margin( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$data_value_name = isset( $data['value']['same'] ) ? $data['value']['same'] : '';
	$data_value_top = isset( $data['value']['top'] ) ? $data['value']['top'] : '';
	$data_value_right = isset( $data['value']['right'] ) ? $data['value']['right'] : '';
	$data_value_bottom = isset( $data['value']['bottom'] ) ? $data['value']['bottom'] : '';
	$data_value_left = isset( $data['value']['left'] ) ? $data['value']['left'] : '';
	$individuals = '';
	$checked = '';
	$same = '';
	if ( isset( $data['value'] ) && isset( $data['value']['checkbox'] ) && $data['value']['checkbox'] ) {
		$checked = "checked='checked'";
		$individuals = "style='display:none;'";	
	} else {
		$same = "style='display:none;'";
	}
	$output = '<div>
					<p>
						<span class="label">' . __('Margin', 'themify') . '</span>';
	$output .= '<span class="same" '.$same.'>
					<input type="text" class="width2 valid_num" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-margin-value-same' ) . '" value="' . esc_attr( $data_value_name ) . '" /> <small>px</small>
				</span>
				<span class="individuals" '.$individuals.'>
					<input type="text" class="width2 valid_num" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-margin-value-top' ) . '" value="' . esc_attr( $data_value_top ) . '" /> ' . __( 'top', 'themify' ) . '
					<input type="text" class="width2 valid_num" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-margin-value-right' ) . '" value="' . esc_attr( $data_value_right ) . '" /> ' . __( 'right', 'themify' ) . '
					<input type="text" class="width2 valid_num"  name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-margin-value-bottom' ) . '" value="' . esc_attr( $data_value_bottom ) . '"/> ' . __( 'bottom', 'themify' ) . '
					<input type="text" class="width2 valid_num" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-margin-value-left' ) . '" value="' . esc_attr( $data_value_left ) . '" /> ' . __( 'left', 'themify' ) . ' <small>(px)</small>
				</span>
			</p>
			<p>
				<span class="pushlabel" style="display:block;">
					<input type="checkbox" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-margin-value-checkbox' ) . '" '.$checked.' class="margin-switch" /> ' . __('Same for all', 'themify') . '
				</span>
			</p>
		</div>';
	return $output;
}

///////////////////////////////////////////
// Height Module
///////////////////////////////////////////
function themify_height( $data = array() ) {
	$value = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$value['value'] = isset( $value['value'] ) ? $value['value'] : '';
	$value['unit'] = isset( $value['unit'] ) ? $value['unit'] : '';
	
	$options = array('px','em','%');
	$output = '	<p><span class="label">' . __('Height', 'themify') . '</span> 
				<input type="text" class="width2 valid_num" value="' . esc_attr( $value['value'] ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-height-value-value' ) . '" />
				<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-height-value-unit' ) . '"><option> </option>';
	foreach($options as $option){
		if($option == $value['unit']){
			$output .= '<option value="' . esc_attr( $option ) . '" selected="selected">' . esc_html( $option ) . '</option>';
		} else {
			$output .= '<option value="' . esc_attr( $option ) . '">' . esc_html( $option ) . '</option>';
		}
	}
	$output .= '</select></p>';
	return $output;
}

///////////////////////////////////////////
// Width Module
///////////////////////////////////////////
function themify_width( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$options = array('px','em','%');
	$output = '	<p><span class="label">' . __('Width', 'themify') . '</span> 
				<input type="text" class="width2 valid_num" value="' . esc_attr( $data['value'] ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-width-value-value' ) . '" />
				<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-width-value-unit' ) . '"><option> </option>';
	foreach($options as $option){
		if($option == $data['value']['unit']){
			$output .= '<option value="' . esc_attr( $option ) . '" selected="selected">' . esc_html( $option ) . '</option>';
		} else {
			$output .= '<option value="' . esc_attr( $option ) . '">' . esc_html( $option ) . '</option>';
		}
	}
	$output .= '</select></p>';
	return $output;
}

///////////////////////////////////////////
// Border Module
///////////////////////////////////////////
function themify_border( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$individuals = '';
	$checked = '';
	$same = '';
	if ( isset( $data['value'] ) && isset( $data['value']['checkbox'] ) && $data['value']['checkbox'] ) {
		$checked = "checked='checked'";
		$individuals = "style='display:none;'";	
	} else {
		$same = "style='display:none;'";
	}
	$options = array(
		array(
			'value' => 'solid',
			'name' => __('Solid', 'themify')
		),
		array(
			'value' => 'dashed',
			'name' => __('Dashed', 'themify')
		),
		array(
			'value' => 'dotted',
			'name' => __('Dotted', 'themify')
		),
		array(
			'value' => 'double',
			'name' => __('Double', 'themify')
		)
	);
	$data_value_same = isset( $data['value']['same'] ) ? $data['value']['same'] : '';
	$data_value_same_color = isset( $data['value']['same_color'] ) ? $data['value']['same_color'] : '';
	$data_value_top_color = isset( $data['value']['top_color'] ) ? $data['value']['top_color'] : '';
	$data_value_top = isset( $data['value']['top'] ) ? $data['value']['top'] : '';
	$data_value_right_color = isset( $data['value']['right_color'] ) ? $data['value']['right_color'] : '';
	$data_value_right = isset( $data['value']['right'] ) ? $data['value']['right'] : '';
	$data_value_bottom_color = isset( $data['value']['bottom_color'] ) ? $data['value']['bottom_color'] : '';
	$data_value_bottom = isset( $data['value']['bottom'] ) ? $data['value']['bottom'] : '';
	$data_value_left_color = isset( $data['value']['left_color'] ) ? $data['value']['left_color'] : '';
	$data_value_left = isset( $data['value']['left'] ) ? $data['value']['left'] : '';
	$output = '<div><p>
					<span class="label">' . __('Border', 'themify') . '</span> 
					<span class="same" '.$same.'>
						<span class="colorSelect" style="' . esc_attr( 'background-color:#' . $data_value_same_color . ';' ). '"><span></span></span>  <input type="text" value="' . esc_attr( $data_value_same_color ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-same_color' ) . '" class="width4 colorSelectInput" />
						<input type="text" class="width2 valid_num" value="' . esc_attr( $data_value_same ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-same' ) . '" /> <small>px</small>
						<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-same_style' ) . '"><option> </option>';
						foreach ( $options as $option ) {
							if ( isset( $data['value']['same_style'] ) && ( $option['value'] == $data['value']['same_style'] ) ) {
								$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
							} else {
								$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
							}
						}
	$output .= '		</select>
					</span>
					<span class="individuals" '.$individuals.'>
					<span class="borders">
						<span class="colorSelect" style="' . esc_attr( 'background-color:#' . $data_value_top_color . ';' ). '"><span></span></span> <input type="text" value="' . esc_attr( $data_value_top_color ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-top_color' ) . '" class="width4 colorSelectInput" />
						<input type="text" class="width2 valid_num" value="' . esc_attr( $data_value_top ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-top' ) . '" /> <small>px</small>
						<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-top_style' ) . '"><option> </option>';
						foreach ( $options as $option ) {
							if ( isset( $data['value']['top_style'] ) && ( $option['value'] == $data['value']['top_style'] ) ) {
								$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
							} else {
								$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
							}
						}
	$output .= '		</select> ' . __('top', 'themify') . '
					</span>
					<span class="pushlabel borders" style="display:block;">
						<span class="colorSelect" style="' . esc_attr( 'background-color:#' . $data_value_right_color . ';' ). '"><span></span></span> <input type="text" value="' . esc_attr( $data_value_right_color ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-right_color' ) . '" class="width4 colorSelectInput" />
						<input type="text" class="width2 valid_num" value="' . esc_attr( $data_value_right ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-right' ) . '" /> <small>px</small>
						<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-right_style' ) . '"><option> </option>';
						foreach ( $options as $option ) {
							if ( isset( $data['value']['right_style'] ) && ( $option['value'] == $data['value']['right_style'] ) ) {
								$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
							} else {
								$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
							}
						}
	$output .= '		</select> ' . __('right', 'themify') . '
					</span>
					<span class="pushlabel borders" style="display:block;">
						<span class="colorSelect" style="' . esc_attr( 'background-color:#' . $data_value_bottom_color . ';' ). '"><span></span></span> <input type="text" value="' . esc_attr( $data_value_bottom_color ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-bottom_color' ) . '" class="width4 colorSelectInput" />
						<input type="text" class="width2 valid_num" value="' . esc_attr( $data_value_bottom ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-bottom' ) . '" /> <small>px</small>
						<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-bottom_style' ) . '"><option> </option>';
						foreach ( $options as $option ) {
							if ( isset( $data['value']['bottom_style'] ) && ( $option['value'] == $data['value']['bottom_style'] ) ) {
								$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
							} else {
								$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
							}
						}
	$output .= '		</select> ' . __('bottom', 'themify') . '
					</span>
					<span class="pushlabel borders" style="display:block;">
						<span class="colorSelect" style="' . esc_attr( 'background-color:#' . $data_value_left_color . ';' ). '"><span></span></span> <input type="text" value="' . esc_attr( $data_value_left_color ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-left_color' ) . '" class="width4 colorSelectInput" />
						<input type="text" class="width2 valid_num" value="' . esc_attr( $data_value_left ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-left' ) . '" /> <small>px</small>
						<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-left_style' ) . '"><option> </option>';
						foreach ( $options as $option ) {
							if ( isset( $data['value']['left_style'] ) && ( $option['value'] == $data['value']['left_style'] ) ) {
								$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
							} else {
								$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
							}
						}
	$output .= '		</select> ' . __('left', 'themify') . '
					</span>
				</span>
				</p>
				<p>
					<span class="pushlabel" style="display:block;">
						<input type="checkbox" class="border-switch" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-border-value-checkbox' ) . '" '.$checked.' /> ' . __('Same for all', 'themify') . '
					</span>
				</p>
			</div>';				
	return $output;
}

///////////////////////////////////////////
// Position Module
///////////////////////////////////////////
function themify_position( $data = array() ) {
	$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$data_value_position = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
	$data_value_x_value = isset( $data['value']['x_value'] ) ? $data['value']['x_value'] : '';
	$data_value_y_value = isset( $data['value']['y_value'] ) ? $data['value']['y_value'] : '';
	$options = array(
		array(
			'value' => 'static',
			'name' => __('static', 'themify')
		),
		array(
			'value' => 'fixed',
			'name' => __('fixed', 'themify')
		),
		array(
			'value' => 'relative',
			'name' => __('relative', 'themify')
		),
		array(
			'value' => 'absolute',
			'name' => __('absolute', 'themify')
		),
		
	);
	$options_x = array(
		array(
			'value' => 'top',
			'name' => __('top', 'themify')
		),
		array(
			'value' => 'bottom',
			'name' => __('bottom', 'themify')
		),			
	);
	$options_y = array(
		array(
			'value' => 'left',
			'name' => __('left', 'themify')
		),
		array(
			'value' => 'right',
			'name' => __('right', 'themify')
		)
	);
	$display = '';
	if ( $data_value_position != 'fixed' && $data_value_position != 'absolute' ) {
		$display = "style='display:none;'";	
	}
	$output = '	<p><span class="label">' . __('Position', 'themify') . '</span>
				<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-position-value-value" class="select_position' ) . '"><option> </option>';
	foreach ( $options as $option ) {
		if ( $option['value'] == $data_value_position ) {
			$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
		} else {
			$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
		}
	}
	$output .= '</select></p>
				<p class="position_display" '.$display.'>
					<span class="pushlabel" style="display:block;">
					<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-position-value-x' ) . '"><option> </option>';
					foreach ( $options_x as $option ) {
						if ( isset( $data['value']['x'] ) && ( $option['value'] == $data['value']['x'] ) ) {
							$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
						} else {
							$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
						}
					}	
	$output .=	'	</select>
					<input type="text" class="width2 valid_num" value="' . esc_attr( $data_value_x_value ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-position-value-x_value' ) . '" /> <small>px</small>
					</span>
					<span class="pushlabel" >
					<select name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-position-value-y' ) . '"><option> </option>';
					foreach ( $options_y as $option ) {
						if ( isset( $data['value']['y'] ) && ( $option['value'] == $data['value']['y'] ) ) {
							$output .= '<option value="'.esc_attr( $option['value'] ).'" selected="selected">' . esc_html( $option['name'] ) . '</option>';
						} else {
							$output .= '<option value="'.esc_attr( $option['value'] ).'">' . esc_html( $option['name'] ) . '</option>';
						}
					}	
	$output .=	'	</select>
					<input type="text" class="width2 valid_num" value="' . esc_attr( $data_value_y_value ) . '" name="' . esc_attr( 'styling-'.$data['category'].'-'.$data['id'].'-position-value-y_value' ) . '" /> <small>px</small>
					</span>
				</p>';
	return $output;	
}

/**
 * Outputs module for user to select whether to use a lightbox or not. The lightbox choices can be filtered using the 'themify_lightbox_module_options' filter in a custom-functions.php file.
 * @param array $data
 * @return string
 * @since 1.2.5
 */
function themify_gallery_plugins( $data = array() ) {

	$display_options = apply_filters('themify_lightbox_module_options', array(
		__( 'Magnific (lightbox)', 'themify' ) => 'lightbox',
		__( 'None', 'themify' ) => 'none'
	));

	$gallery_lightbox = themify_get( 'setting-gallery_lightbox' );

	$out = '<p>
				<span class="label">' . __( 'WordPress Gallery', 'themify' ) . ' </span>
				<select class="gallery_lightbox_type" name="setting-gallery_lightbox">';
				foreach ( $display_options as $option => $value ) {
					$out .= '<option value="' . esc_attr( $value ) . '" '.selected( $value? $value: 'lightbox', $gallery_lightbox, false ).'>' . esc_html( $option ) . '</option>';
				}
	$out .= '	</select>
			</p>';
	$out .= '<p>
				<span class="pushlabel"><label for="setting-lightbox_content_images">
					<input type="checkbox" id="setting-lightbox_content_images" name="setting-lightbox_content_images" '. checked( themify_get( 'setting-lightbox_content_images' ), 'on', false ) .'/> ' . __('Apply lightbox to image links (ie. links to jpg, png, and gif will open in lightbox)', 'themify') . '</label>
				</span>
			</p>';
	return $out;
}

/**
 * Template to display a link in Links module, also used when creating a link.
 * @param array $data
 * @return string
 * @since 1.2.7
 */
function themify_add_link_template( $fid, $data = array(), $ajax = false, $type = 'image-icon' ) {
	$pre = 'setting-link_';
	
	$type_name = $pre.'type_'.$fid;
	if ( $ajax ) {
		$type_val = $type;
	} else {
		$type_val = isset($data[$type_name])? $data[$type_name] : 'image-icon';
	}

	$title_name = $pre.'title_'.$fid;
	$title_val = isset($data[$title_name])? $data[$title_name] : '';
	
	$link_name = $pre.'link_'.$fid;
	$link_val = isset($data[$link_name])? $data[$link_name] : '';
	
	$img_name = $pre.'img_'.$fid;
	$img_val = ! isset( $data[$img_name] ) || '' == $data[$img_name]? '' : $data[$img_name];

	$ficon_name = $pre.'ficon_'.$fid;
	$ficon_val = trim( isset($data[$ficon_name])? $data[$ficon_name] : '' );

	$ficolor_name = $pre.'ficolor_'.$fid;
	$ficolor_val = isset($data[$ficolor_name])? $data[$ficolor_name] : '';

	$fibgcolor_name = $pre.'fibgcolor_'.$fid;
	$fibgcolor_val = isset($data[$fibgcolor_name])? $data[$fibgcolor_name] : '';

	/**
	 * TODO: Add appearance checkboxes
	 */

	$out = '<li id="' . esc_attr( $fid ) . '" class="social-link-item ' . esc_attr( $type_val ) . '">';

	$out .= '<div class="social-drag">' . esc_html__('Drag to Sort', 'themify') . '<i class="ti-arrows-vertical"></i></div>';

	$out .= '<input type="hidden" name="' . esc_attr( $type_name ) . '" value="' . esc_attr( trim( $type_val ) ) . '">';

	$out .= '<div class="row">
				<span class="label">' . __( 'Title', 'themify' ) . '</span> <input type="text" name="' . esc_attr( $title_name ) . '" class="width6" value="' . esc_attr( trim($title_val) ) . '">
			</div>
			<!-- /row -->';

	$out .= '<div class="row">
				<span class="label">' . __( 'Link', 'themify' ) . '</span> <input type="text" name="' . esc_attr( $link_name ) . '" class="width10" value="' . esc_attr( trim($link_val) ) . '">
			</div>
			<!-- /row -->';

	if ( 'font-icon' == $type_val ) {

		$out .= '<div class="row">
					<span class="label">' . __( 'Icon', 'themify' ) . '</span>';

		$out .= sprintf('<input type="text" id="%s" name="%s" value="%s" size="55" class="themify_input_field themify_fa %s" /> <a class="button button-secondary hide-if-no-js themify_fa_toggle" href="#" data-target="#%s">%s</a>',
			esc_attr( $ficon_name ), esc_attr( $ficon_name ), esc_attr( $ficon_val ), 'small', esc_attr( $ficon_name ), __( 'Insert Icon', 'themify' ) );

		$out .= '</div>
				<!-- /row -->';

		$out .= '<div class="icon-preview font-icon-preview">
						<i class="' . esc_attr( 'fa ' . $ficon_val ) . '"></i>
					</div>
					<!-- /icon-preview -->';

		$out .= '<div class="themify_field_row">
					<span class="label">' . __('Icon Color', 'themify') . '</span>
					<div class="themify_field-color">
						<span class="colorSelect" style="' . esc_attr( 'background:#' . $ficolor_val . ';' ). '">
							<span></span>
						</span>
						<input type="text" class="colorSelectInput width4" value="' . esc_attr( $ficolor_val ) . '" name="' . esc_attr( $ficolor_name ) . '" />
					</div>
				</div>';

		$out .= '<div class="themify_field_row">
					<span class="label">' . __('Background', 'themify') . '</span>
					<div class="themify_field-color">
						<span class="colorSelect" style="' . esc_attr( 'background:#' . $fibgcolor_val . ';' ). '">
							<span></span>
						</span>
						<input type="text" class="colorSelectInput width4" value="' . esc_attr( $fibgcolor_val ) . '" name="' . esc_attr( $fibgcolor_name ) . '" />
					</div>
				</div>';

	} else {

		$out .= '<div class="row">
					<span class="label">' . __( 'Image', 'themify' ) . '</span>
					<div class="uploader-fields image">
						<input type="text" id="' . esc_attr( $img_name ) . '" name="' . esc_attr( $img_name ) . '" class="width10" value="' . esc_attr( $img_val ) . '">
						<div class="clear image">' . themify_get_uploader( $img_name, array( 'tomedia' => true, 'preview' => true ) ) . '</div>
					</div>
				</div>
				<!-- /row -->';
		$out .= '<div class="icon-preview">
					<img id="' . esc_attr( $img_name . '-preview' ) . '" src="' . esc_url( $img_val ) . '" />
				</div>
				<!-- /icon-preview -->';
	}

	$out .= '<a href="#" class="remove-item" data-removelink="' . esc_attr( $fid ) . '"><i class="ti ti-close"></i></a>
		</li>
		<!-- /social-links-item -->';

	return $out;
}

/**
 * Outputs module to manage links to be shown using the corresponding widget
 * @param array $data
 * @return string
 * @since 1.2.7
 */
function themify_manage_links( $data = array() ) {
	$data = themify_get_data();
	$pre = 'setting-link_';
	$field_hash = isset( $data[$pre.'field_hash'] ) && $data[$pre.'field_hash'] ? $data[$pre.'field_hash'] : 10;
	$start = array();
	for ( $i=0; $i < $field_hash; $i++ ) {
		$start['themify-link-'.$i] = 'themify-link-'.$i;
	}
	//$data[$pre.'field_ids'] = json_encode($start);
	
	if ( json_decode( themify_get( $pre.'field_ids' ) ) ) {
		$field_ids = json_decode( themify_get( $pre.'field_ids' ) );
	} else {
		$field_ids = $start;

		// Image Icons

		$data[$pre.'type_themify-link-0'] = 'image-icon';
		$data[$pre.'type_themify-link-1'] = 'image-icon';
		$data[$pre.'type_themify-link-2'] = 'image-icon';
		$data[$pre.'type_themify-link-3'] = 'image-icon';
		$data[$pre.'type_themify-link-4'] = 'image-icon';

		$data[$pre.'title_themify-link-0'] = 'Twitter';
		$data[$pre.'title_themify-link-1'] = 'Facebook';
		$data[$pre.'title_themify-link-2'] = 'Google+';
		$data[$pre.'title_themify-link-3'] = 'YouTube';
		$data[$pre.'title_themify-link-4'] = 'Pinterest';
		
		$data[$pre.'link_themify-link-0'] = '';
		$data[$pre.'link_themify-link-1'] = '';
		$data[$pre.'link_themify-link-2'] = '';
		$data[$pre.'link_themify-link-3'] = '';
		$data[$pre.'link_themify-link-4'] = '';
		
		$data[$pre.'img_themify-link-0'] = THEMIFY_URI . '/img/social/twitter.png';
		$data[$pre.'img_themify-link-1'] = THEMIFY_URI . '/img/social/facebook.png';
		$data[$pre.'img_themify-link-2'] = THEMIFY_URI . '/img/social/google-plus.png';
		$data[$pre.'img_themify-link-3'] = THEMIFY_URI . '/img/social/youtube.png';
		$data[$pre.'img_themify-link-4'] = THEMIFY_URI . '/img/social/pinterest.png';

		// Font Icons

		$data[$pre.'type_themify-link-5'] = 'font-icon';
		$data[$pre.'type_themify-link-6'] = 'font-icon';
		$data[$pre.'type_themify-link-7'] = 'font-icon';
		$data[$pre.'type_themify-link-8'] = 'font-icon';
		$data[$pre.'type_themify-link-9'] = 'font-icon';

		$data[$pre.'title_themify-link-5'] = 'Twitter';
		$data[$pre.'title_themify-link-6'] = 'Facebook';
		$data[$pre.'title_themify-link-7'] = 'Google+';
		$data[$pre.'title_themify-link-8'] = 'YouTube';
		$data[$pre.'title_themify-link-9'] = 'Pinterest';

		$data[$pre.'link_themify-link-5'] = '';
		$data[$pre.'link_themify-link-6'] = '';
		$data[$pre.'link_themify-link-7'] = '';
		$data[$pre.'link_themify-link-8'] = '';
		$data[$pre.'link_themify-link-9'] = '';

		$data[$pre.'ficon_themify-link-5'] = 'fa-twitter';
		$data[$pre.'ficon_themify-link-6'] = 'fa-facebook';
		$data[$pre.'ficon_themify-link-7'] = 'fa-google-plus';
		$data[$pre.'ficon_themify-link-8'] = 'fa-youtube';
		$data[$pre.'ficon_themify-link-9'] = 'fa-pinterest';

		$data[$pre.'ficolor_themify-link-5'] = '';
		$data[$pre.'ficolor_themify-link-6'] = '';
		$data[$pre.'ficolor_themify-link-7'] = '';
		$data[$pre.'ficolor_themify-link-8'] = '';
		$data[$pre.'ficolor_themify-link-9'] = '';

		$data[$pre.'fibgcolor_themify-link-5'] = '';
		$data[$pre.'fibgcolor_themify-link-6'] = '';
		$data[$pre.'fibgcolor_themify-link-7'] = '';
		$data[$pre.'fibgcolor_themify-link-8'] = '';
		$data[$pre.'fibgcolor_themify-link-9'] = '';
		
		$data = apply_filters('themify_default_social_links', $data);
	}

	$out = '<div class="themify-info-link">' . sprintf( __( 'To display the links: go to Appearance > <a href="%s">Widgets</a> and drop a Themify - Social Links widget in a widget area (<a href="%s" target="_blank">learn more</a>)', 'themify' ), admin_url('widgets.php'), 'https://themify.me/docs/social-media-links') . '</div>';

	$out .= '<div id="social-link-type">';
		// Icon Font
		$out .= '<label for="' . esc_attr( $pre ) . 'font_icon">';
		$out .= '<input ' . checked( isset( $data[$pre.'icon_type'] )? $data[$pre.'icon_type'] : 'font-icon', 'font-icon', false) . ' type="radio" id="' . esc_attr( $pre . 'font_icon' ) . '" name="' . esc_attr( $pre . 'icon_type' ) . '" value="font-icon" data-hide="image-icon" /> ';
		$out .= __( 'Icon Font', 'themify' ) . '</label>';

		// Image
		$out .= '<label for="' . esc_attr( $pre ) . 'image_icon">';
		$out .= '<input ' . checked( isset( $data[$pre.'icon_type'] )? $data[$pre.'icon_type'] : '', 'image-icon', false ) . ' type="radio" id="' . esc_attr( $pre . 'image_icon' ) . '" name="' . esc_attr( $pre . 'icon_type' ) . '" value="image-icon" data-hide="font-icon" /> ';
		$out .= __( 'Image', 'themify' ) . '</label>';
	$out .= '</p>';

	$out .=  '<ul id="social-links-wrapper">';
		foreach ( $field_ids as $key => $fid ) {
			$out .= themify_add_link_template( $fid, $data );
		}
	$out .= '</ul>';
	
	$out .= '<p class="add-link add-social-link"><a href="#">' . __('Add Link', 'themify') . '</a></p>';

	$out .= '<input type="hidden" id="' . esc_attr( $pre . 'field_ids' ) . '" name="' . esc_attr( $pre . 'field_ids' ) . '" value=\'' . json_encode( $field_ids ) . '\'/>';
	$out .= '<input type="hidden" id="' . esc_attr( $pre . 'field_hash' ) . '" name="' . esc_attr( $pre . 'field_hash' ) . '" value="' . esc_attr( $field_hash ) . '"/>';
	//$out .= '<p>Fields: '.json_encode($field_ids).'</p><p>Hash: '.$field_hash.'</p>';
	
	return $out;
}

/**
 * Disables Open Graph tags. This function does nothing. It's merely kept so users that might have erroneously customized the theme-config.php copying it and directing it to a new file don't get their sites broken.
 * @deprecated 1.7.5
 * @param array $data
 * @return string $out Markup for option
 */
function themify_open_graph_options($data = array()){
	return '';
}

/**
 * Outputs post meta options
 * @param string $pmkey Key used to get data from theme settings array
 * @param array $data Theme settings data
 * @param array $metas Optional array stating the metas available.
 * @return string $out Markup for options
 */
function themify_post_meta_options( $pmkey, $data, $metas = array(), $states = array(), $group_label = false ) {
	
	if ( empty($metas ) ) {
		$metas = array (
			''			=> __( 'Hide All', 'themify' ),
	 		'author' 	=> __( 'Author', 'themify' ),
	 		'category' 	=> __( 'Category', 'themify' ),
	 		'comment' 	=> __( 'Comment', 'themify' ),
	 		'tag' 		=> __( 'Tag', 'themify' )
		);
	}
	if ( empty( $states ) ) {
		$states = array(
			array(
				'name' => __( 'Hide', 'themify' ),
				'value' => 'yes',
				'icon' => THEMIFY_URI . '/img/ddbtn-check.png',
				'title' => __( 'Hide this meta', 'themify' )
			),
			array(
				'name' => __( 'Do not hide', 'themify' ),
				'value' => 'no',
				'icon' => THEMIFY_URI . '/img/ddbtn-cross.png',
				'title' => __( 'Show this meta', 'themify' )
			)
		);
	}
	if ( ! $group_label ) {
		$group_label = __( 'Hide Post Meta', 'themify' );
	}
	
	$default = array(
		'name' => __( 'Theme', 'themify' ),
		'value' => '',
		'icon' => THEMIFY_URI . '/img/ddbtn-blank.png',
		'title' => __( 'Use theme settings', 'themify' )
	);
	
	$out = '<div class="themify_field_row dropdownbutton-group"><span class="label">' . esc_html( $group_label ) . '</span>';
					
			foreach ( $metas as $meta => $name ) {
				if ( '' == $meta ) {
					$metakey = $pmkey;
					$meta_class = 'ddbtn-all';
				} else {
					$metakey = $pmkey.'_'.$meta;
					$meta_class = 'ddbtn-sub ddbtn-'.$meta;
				}

				$others = '';
				$out .=	'
				<div id="' . esc_attr( $metakey ) . '" class="dropdownbutton-list" data-name="' . esc_attr( $name ) . '" data-def-icon="' . esc_url( $default['icon'] ) . '">';
				
				// default state
				$first = '
					<div class="first-ddbtn">
						<a href="#" data-val="' . esc_attr( $default['value'] ) . '" data-name="' . esc_attr( $default['name'] ) . '" title="' . esc_attr( $default['title'] ) . '">
							<img src="' . esc_url( $default['icon'] ) . '" title="' . esc_attr( $default['title'] ) . '" />
							<span class="ddbtn-name">' . esc_html( $name ) . '</span>
						</a>
					</div>';

				foreach ( $states as $state ) {
					if ( isset( $state['value'] ) && isset( $data[$metakey] ) && $state['value'] == $data[$metakey] ) {
						$first = '
						<div class="first-ddbtn">
							<a href="#" data-val="' . esc_attr( $state['value'] ) . '" data-name="' . esc_attr( $state['name'] ) . '" title="' . esc_attr( $state['title'] ) . '">
								<img src="' . esc_url( $state['icon'] ) . '" title="' . esc_attr( $state['title'] ) . '" />
								<span class="ddbtn-name">' . esc_html( $name ) . '</span>
							</a>
						</div>';
						$selected = 'selected';
						//$hide = 'ddbtn-hide';
						$hide = '';
					} else {
						$selected = '';
						$hide = '';
					}
					
					$others .= '
						<div class="' . esc_attr( 'ddbtn ' . $hide ) . '">
							<a href="#" data-sel="' . esc_attr( $selected ) . '" data-val="' . esc_attr( $state['value'] ) . '" data-name="' . esc_attr( $state['name'] ) . '" title="' . esc_attr( $state['title'] ) . '">
								<img src="' . esc_url( $state['icon'] ) . '" title="' . esc_attr( $state['title'] ) . '" />
								<span class="ddbtn-label">' . esc_html( $state['name'] ) . '</span>
							</a>
						</div>';
				}
				$out .= $first . '<div class="dropdownbutton">' . $others . '</div>';
				$out .= '
				</div>';
				$meta_key_data = themify_get( $metakey );
				$out .= '<input type="hidden" value="' . esc_attr( $meta_key_data ) . '" class="' . esc_attr( $meta_class ) . '" id="' . esc_attr( $metakey ) . '" name="' . esc_attr( $metakey ) . '" />';
			}

	$out .= '</div>';
	return $out;
}

/**
 * Outputs post sorting options
 * @param string $key Key used to get data from theme settings array
 * @param array $data Theme settings data
 * @return string $out Markup for options
 */
if ( ! function_exists( 'themify_post_sorting_options' ) ) {
	function themify_post_sorting_options( $key = 'setting-index_order', $data ) {

		$orderby = themify_get( $key . 'by' );
		$orderby_options = apply_filters( 'themify_index_orderby_options', array(
			__( 'Date (default)', 'themify' ) => 'date',
			__( 'Random', 'themify' ) => 'rand',
			__( 'Author', 'themify' ) => 'author',
			__( 'Post Title', 'themify' ) => 'title',
			__( 'Comments Number', 'themify' ) => 'comment_count',
			__( 'Modified Date', 'themify' ) => 'modified',
			__( 'Post Slug', 'themify' ) => 'name',
			__( 'Post ID', 'themify' ) => 'ID',
			__( 'Custom Field String', 'themify' ) => 'meta_value',
			__( 'Custom Field Numeric', 'themify' ) => 'meta_value_num' ) );

		$order = themify_get( $key );
		$order_options = array(
			__( 'Descending (default)', 'themify' ) => 'DESC',
			__( 'Ascending', 'themify' ) => 'ASC' );

		$order_meta_key = 'setting-index_meta_key';
		$order_meta_key_value = themify_get( $order_meta_key );

		$out = '<p>
					<span class="label">' . __( 'Order By', 'themify' ) . ' </span>
					<select name="' . esc_attr( $key . 'by' ) . '">';
						foreach ( $orderby_options as $option => $value ) {
							$out .= '<option value="' . esc_attr( $value ) . '" '.selected( $value? $value: 'date', $orderby, false ).'>' . esc_html( $option ) . '</option>';
							}
		$out .= '	</select>
				</p>
				<p data-show-if-element="[name=' . $key . 'by]" data-show-if-value=\'["meta_value", "meta_value_num"]\'>
					<span class="label">' . __( 'Custom Field Key', 'themify' ) . ' </span>
					<input type="text" id="' . esc_attr( $order_meta_key ) . '" name="' . esc_attr( $order_meta_key ) . '" value="' . esc_attr( $order_meta_key_value ) . '" />
				</p>
				<p>
					<span class="label">' . __( 'Order', 'themify' ) . ' </span>
					<select name="' . esc_attr( $key ) . '">';
						foreach ( $order_options as $option => $value ) {
							$out .= '<option value="' . esc_attr( $value ) . '" '.selected( $value? $value: 'DESC', $order, false ).'>' . esc_html( $option ) . '</option>';
					}
		$out .= '	</select>
				</p>';

		return $out;
	}
}

if ( ! function_exists( 'themify_homepage_welcome' ) ) {
	/**
	 * Homepage Welcome Function
	 * @return string Markup for welcome text control
	 */
	function themify_homepage_welcome() {
		return '<p><textarea class="widthfull" name="setting-homepage_welcome" rows="4">' . esc_textarea( themify_get( 'setting-homepage_welcome' ) ) . '</textarea></p>';
	}
}

if ( ! function_exists( 'themify_exclude_rss' ) ) {
	/**
	 * Exclude RSS
	 * @return string
	 */
	function themify_exclude_rss() {
		return '<p><label for="setting-exclude_rss"><input type="checkbox" id="setting-exclude_rss" name="setting-exclude_rss" ' . checked( themify_get( 'setting-exclude_rss' ), 'on', false ) . '/> ' . __( 'Check here to exclude RSS icon/button in the header', 'themify' ) . '</label></p>';	
	}
}

if ( ! function_exists( 'themify_exclude_search_form' ) ) {
	/**
	 * Exclude Search Form
	 * @return string
	 */
	function themify_exclude_search_form() {
		return '<p><label for="setting-exclude_search_form"><input type="checkbox" id="setting-exclude_search_form" name="setting-exclude_search_form" ' . checked( themify_get( 'setting-exclude_search_form' ), 'on', false ) . '/> ' . __( 'Check here to exclude search form in the header', 'themify' ) . '</label></p>';	
	}
}

if( ! function_exists( 'footer_text_settings' ) ) {
	/**
	 * Footer Text Settings
	 * @return string
	 */
	function footer_text_settings() {
		return '<div class="themify-info-link">' . __( 'Enter your text to replace the copyright and credit links in the footer. HTML tags allowed.', 'themify' ) . '</div>' . themify_footer_text_left() . themify_footer_text_right();
	}
}

if ( ! function_exists( 'themify_footer_text_left' ) ) {
	/**
	 * Footer Text Left Function
	 * @return string
	 */
	function themify_footer_text_left() {
		return '<h4>' . __('Footer Text One', 'themify') . '</h4><div data-show-if-element="[name=setting-footer_text_left_hide]" data-show-if-value="false"><textarea class="widthfull" rows="4" name="setting-footer_text_left">' . esc_textarea( themify_get( 'setting-footer_text_left' ) ) . '</textarea></div><div><label><input type="checkbox" name="setting-footer_text_left_hide" value="hide" ' . checked( themify_get( 'setting-footer_text_left_hide' ), 'hide', false ) . ' />' . __( 'Hide Footer Text One', 'themify' ) . '</label></div>';
	}
}

if ( ! function_exists( 'themify_footer_text_right' ) ) {
	/**
	 * Footer Text Right Function
	 * @return string
	 */
	function themify_footer_text_right(){
		return '<h4>' . __('Footer Text Two', 'themify') . '</h4><div data-show-if-element="[name=setting-footer_text_right_hide]" data-show-if-value="false"><textarea class="widthfull" rows="4" name="setting-footer_text_right">' . esc_textarea( themify_get( 'setting-footer_text_right' ) ) . '</textarea></div><div><label><input type="checkbox" name="setting-footer_text_right_hide" value="hide" ' . checked( themify_get( 'setting-footer_text_right_hide' ), 'hide', false ) . ' />' . __( 'Hide Footer Text Two', 'themify' ) . '</label></div>';
	}
}

if(!function_exists('themify_homepage_widgets')){
	/**
	 * Widgets module function
	 * @return string Module markup
	 */
	function themify_homepage_widgets(){
		$data = themify_get_data();
		$val = isset($data['setting-homepage_widgets'])? $data['setting-homepage_widgets'] : null;
		$options = array(
			array(
				'value' => 'homewidget-4col',
				'img' => 'themify/img/sidebars/4col.png',
				'title' => __('Widgets 4 Columns', 'themify')),
			array(
				'value' => 'homewidget-3col',
				'img' => 'themify/img/sidebars/3col.png',
				'title' => __('Widgets 3 Columns', 'themify'),
				'selected' => true),
			array(
				'value' => 'homewidget-2col',
				'img' => 'themify/img/sidebars/2col.png',
				'title' => __('Widgets 3 Columns', 'themify')),
			array(
				'value' => 'homewidget-1col',
				'img' => 'themify/img/sidebars/1col.png',
				'title' => __('Widgets 1 Column', 'themify')),
			array(
				'value' => 'none',
				'img' => 'themify/img/sidebars/none.png',
				'title' => __('No Widgets', 'themify'))
		);
		$output = '';
		foreach($options as $option){
			if(('' == $val || !$val || !isset($val)) && isset($option['selected']) && $option['selected']){ 
				$val = $option['value'];
			}
			if($val == $option['value']){ 
				$class = ' selected';
			} else {
				$class = '';
			}
			$output .= '<a href="#" class="' . esc_attr( 'preview-icon' . $class ) . '" title="' . esc_attr( $option['title'] ) . '"><img src="' . esc_url( THEME_URI.'/'.$option['img'] ) . '" alt="' . esc_attr( $option['value'] ) . '"  /></a>';
		}
		$output .= '<input type="hidden" name="setting-homepage_widgets" class="val" value="' . esc_attr( $val ) . '" />';
		return $output;
	}
}

if(!function_exists('themify_footer_widgets')){
	/**
	 * Widgets module function
	 * @return string Module markup
	 */
	function themify_footer_widgets(){
		$data = themify_get_data();
		$val = isset($data['setting-footer_widgets'])? $data['setting-footer_widgets'] : null;
		$options = array(
			array(
				'value' => 'footerwidget-4col',
				'img' => 'themify/img/sidebars/4col.png',
				'title' => __('Widgets 4 Columns', 'themify')),
			array(
				'value' => 'footerwidget-3col',
				'img' => 'themify/img/sidebars/3col.png',
				'title' => __('Widgets 3 Columns', 'themify'),
				'selected' => true),
			array(
				'value' => 'footerwidget-2col',
				'img' => 'themify/img/sidebars/2col.png',
				'title' => __('Widgets 2 Columns', 'themify')),
			array(
				'value' => 'footerwidget-1col',
				'img' => 'themify/img/sidebars/1col.png',
				'title' => __('Widgets 1 Column', 'themify')),
			array(
				'value' => 'none',
				'img' => 'themify/img/sidebars/none.png',
				'title' => __('No Widgets', 'themify'))
		);
		$output = '';
		foreach($options as $option){
			if(('' == $val || !$val || !isset($val)) && isset($option['selected']) && $option['selected']){ 
				$val = $option['value'];
			}
			if($val == $option['value']){ 
				$class = 'selected';
			} else {
				$class = '';
			}
			$output .= '<a href="#" class="' . esc_attr( 'preview-icon ' . $class ) . '" title="' . esc_attr( $option['title'] ) . '"><img src="' . esc_url( THEME_URI.'/'.$option['img'] ) . '" alt="' . esc_attr( $option['value'] ) . '"  /></a>';
		}
		$output .= '<input type="hidden" name="setting-footer_widgets" class="val" value="' . esc_attr( $val ) . '" />';
		return $output;
	}
}

if(!function_exists('themify_manage_twitter_settings')){
	/**
	 * Twitter API Settings
	 * @return string
	 */
	function themify_manage_twitter_settings() {
		$prefix = 'setting-twitter_settings_';
		$consumer_key = themify_get( $prefix.'consumer_key' );
		$consumer_secret = themify_get( $prefix.'consumer_secret' );

		$out = '<div class="themify-info-link">'.sprintf(
			__('<a href="%s">Twitter access</a> is required for Themify Twitter widget and Twitter shortcode, read this <a href="%s">documentation</a> for more details.', 'themify'),
			'https://apps.twitter.com/app/new',
			'https://themify.me/docs/setting-up-twitter'
		)
		.'</div>';
		$out .= '<p><label class="label" for="' . esc_attr( $prefix . 'consumer_key' ) . '">'.__('Consumer Key', 'themify').'</label>';
		$out .= '<input type="text" id="' . esc_attr( $prefix . 'consumer_key' ) . '" name="' . esc_attr( $prefix . 'consumer_key' ) . '" class="width10" value="' . esc_attr( $consumer_key ) . '" /></p>';

		$out .= '<p><label class="label" for="' . esc_attr( $prefix . 'consumer_secret' ) . '">'.__('Consumer Secret', 'themify').'</label>';
		$out .= '<input type="text" id="' . esc_attr( $prefix . 'consumer_secret' ) . '" name="' . esc_attr( $prefix . 'consumer_secret' ) . '" class="width10" value="' . esc_attr( $consumer_secret ) . '" /></p>';

		return $out;
	}
}



if(!function_exists('themify_manage_script_minification_settings')) {
	/**
	 * Script Minification Settings
	 * @param array Themify data
	 * @return string Module markup
	 * @since 1.3.9
	 */
	function themify_manage_script_minification_settings($data = array()){
                $output = '<span class="label">' . __( 'Minify &amp; Compile Scripts', 'themify' ) . '</span>';
                 $key = 'setting-script_minification';
                if(TFCache::check_version()){
                    $value = themify_get( $key );
                    if(!$value){
                        $value = 'disable';
                    }
                    $expire =  themify_get( 'setting-page_builder_expiry' );
                    $expire = $expire>0?intval($expire):2;
                   
                    $output .= '<label><input ' . checked( $value, 'enable', false ). ' type="radio" name="'.$key.'" value="enable" /> ';
                    $output .= __('Enable minification (all Javascript &amp; CSS files will be minified/compiled)', 'themify').'</label>';
                    $output .= '<div style="margin: 5px 0px 5px 35px;" data-show-if-element="[name='.$key.']:checked" data-show-if-value=' . '["enable"]' . '>';
                    $output .= '<label class="pushlabel" for="setting-page_builder_cache"><input  type="checkbox" id="setting-page_builder_cache" name="setting-page_builder_cache" '.checked( themify_check( 'setting-page_builder_cache' ), true, false ).'/> ' . __('Enable Builder Caching (will cache the Builder content)', 'themify').'</label>';
                    $output .= '<label class="pushlabel" for="setting-cache_gzip"><input  type="checkbox" id="setting-cache_gzip" name="setting-cache_gzip" '.checked( themify_check( 'setting-cache_gzip' ), true, false ).'/> ' . __('Enable gzip (will add gzip code in htaccess file)', 'themify').'</label>';
                    $output .='<div class="pushlabel" data-show-if-element="[name='.$key.']:checked" data-show-if-value=' . '["enable"]' . '>';
                    $output .=sprintf('<input type="text" class="width2" value="%s" name="%s" />  &nbsp;&nbsp;<span>%s</span>',$expire,'setting-page_builder_expiry',__( 'Expire Cache (days)', 'themify' ));
                    $output .='<br/><a href="#" data-confim="'.__( 'This will clear all caches. click ok to continue.', 'themify' ).'" data-action="themify_clear_all_caches" data-clearing-text="'.__('Clearing cache...','themify').'" data-done-text="'.__('Done','themify').'" data-default-text="'.__('Clear cache','themify').'" data-default-icon="ti-eraser" class="button button-outline js-clear-minify-cache js-clear-builder-cache"> <i class="ti-eraser"></i> <span>'.__('Clear minified cache','themify').'</span></a><br/>';
                    $output .='<small>'.__('Clear all cache','themify').'</small></div>';
                    $output .='</div>';
                    $output .= '<span class="pushlabel"><label><input ' . checked( $value, 'disable', false ). ' type="radio" name="'.$key.'" value="disable" /> ';
                    $output .= __('Disable minification if you experience frontend issues/conflicts', 'themify') . '</label></span>';

                }
                else{
                    $output.=__('Minification and caching requires 5.4 or abov. Your server does not support it.Please contact your host provider to upgrade php version.','themify');
                }
                        $output .='<p>
                                <span class="label">' . __( 'Minified CSS/JS', 'themify' ) . '</span>
                                <input type="checkbox" id="'.$key.'-min" name="'.$key.'-min" '. checked( themify_get($key.'-min' ), 'on', false ) .'/> ' . __('Do not use minified Javascript &amp; CSS files in the theme', 'themify') . '</label>
                        </p>';
		return $output;
	}
}
if(!function_exists('themify_webfonts_subsets')) {
	/**
	 * Module to specify additional characters subsets
	 * @param array Themify data
	 * @return string Module markup
	 * @since 1.3.9
	 */
	function themify_webfonts_subsets($data = array()){
		

		// List of fonts, recommended or full
		$key = 'setting-webfonts_list';
		$html = '<p>
					<span class="label">' . __('Google Fonts List', 'themify') . '</span>';

			// Disable Google fonts
			$html .= '<label for="' . esc_attr( $key . '_disabled' ) . '">
					<input ' . checked( themify_check( $key ) ? themify_get( $key ) : '', 'disabled', false ) . ' type="radio" id="' . esc_attr( $key . '_disabled' ) . '" name="' . esc_attr( $key ) . '" value="disabled" /> ' .  __( 'Disable Google fonts', 'themify' ) . '</label><br/>';

			// Recommended list
			$html .= '<span class="pushlabel">
					<label for="' . esc_attr( $key . '_recommended' ) . '">
					<input ' . checked( themify_check( $key )? themify_get( $key ) : 'recommended', 'recommended', false) . ' type="radio" id="' . esc_attr( $key . '_recommended' ) . '" name="' . esc_attr( $key ) . '" value="recommended" /> ' .  __( 'Show recommended Google Fonts only', 'themify' ) . '</label><br/>';

			// Full list
			$html .= '
					<label for="' . esc_attr( $key . '_full' ) . '">
					<input ' . checked( themify_check( $key )? themify_get( $key ) : '', 'full', false ) . ' type="radio" id="' . esc_attr( $key . '_full' ) . '" name="' . esc_attr( $key ) . '" value="full" /> ' . __( 'Show all Google Fonts (showing all fonts will take longer to load)', 'themify' ) . '</label>
					</span>
				</p>';
                $subsets = array(
                                'arabic',
                                'bengali',
                                'cyrillic',
                                'cyrillic-ext',
                                'devanagari',
                                'greek',
                                'greek-ext',
                                'gujarati',
                                'gurmukhi',
                                'hebrew',
                                'kannada',
                                'khmer',
                                'korean',
                                'latin',
                                'latin-ext',
                                'malayalam',
                                'myanmar',
                                'oriya',
                                'sinhala',
                                'tamil',
                                'telugu',
                                'thai',
                                'vietnamese'
                                );
		// Filter by character subset
		$key = 'setting-webfonts_subsets';
                $val = themify_get( $key );
                $selected_subset = array();   
                if($val){
                    $selected_subset = explode(',',$val);
                    $selected_subset = array_map('trim',$selected_subset);
                }
		$html .= '<p><span class="label">' . __( 'Character Subsets', 'themify' ) . '</span>';
                $html.='<select size="11" multiple="multiple" class="width10 google_font_subset">';
                foreach($subsets as $s){
                    $selected = in_array($s,$selected_subset)?'selected="selected"':'';
                    $html.='<option '.$selected.' value="'.$s.'">'.$s.'</option>';
                }
                $html.='</select><input type="hidden" name="' . $key . '" value="'.$val.'" /> <br />
				</p>';

		$html .= '<p>
					<span class="pushlabel"><a href="#" class="refresh-webfonts button">'.__( 'Refresh List', 'themify' ).'</a><br/><small>' . __( 'If you made any changes to these settings, refresh the list.', 'themify' ) . '</small></span>
				</p>';

		return $html;
	}
}

if ( ! function_exists( 'themify_entries_navigation' ) ) {
	/**
	 * Display module to select numbered pagination or links to previous and next posts.
	 * @param array $data
	 * @return string $html Module markup.
	 * @since 1.6.0
	 */
	function themify_entries_navigation( $data = array() ) {
		$data = themify_get_data();
		$key = 'setting-entries_nav';
		$html = '<p>';
			// Numbered pagination
			$html .= '<label for="' . esc_attr( $key . '_numbered' ) . '">';
			$html .= '<input ' . checked( isset( $data[$key] )? $data[$key] : 'numbered', 'numbered', false) . ' type="radio" id="' . esc_attr( $key . '_numbered' ) . '" name="' . esc_attr( $key ) . '" value="numbered" /> ';
			$html .= __( 'Numbered Page Navigation (page 1, 2, 3, etc.)', 'themify' ) . '</label>';
			$html .= '<br/>';
			
			// Previous / Next links
			$html .= '<label for="' . esc_attr( $key . '_prevnext' ) . '">';
			$html .= '<input ' . checked( isset( $data[$key] )? $data[$key] : '', 'prevnext', false ) . ' type="radio" id="' . esc_attr( $key . '_prevnext' ) . '" name="' . esc_attr( $key ) . '" value="prevnext" /> ';
			$html .= __( 'Previous Posts and Next Posts Links', 'themify' ) . '</label>';
		$html .= '</p>';
		return $html;
	}
}

//////////////////////////////////////////////
// Add common modules
//////////////////////////////////////////////
if(!function_exists('themify_framework_theme_config_webfonts_subsets')){
	function themify_framework_theme_config_webfonts_subsets($themify_theme_config) {
		$themify_theme_config['panel']['settings']['tab']['general']['custom-module'][] =
			array(
				'title' => __('Google Fonts', 'themify'),
				'function' => 'themify_webfonts_subsets'
			)
		;
		return $themify_theme_config;
	};
	add_filter('themify_theme_config_setup', 'themify_framework_theme_config_webfonts_subsets');
}

if( ! function_exists( 'themify_framework_theme_config_add_twitter_settings' ) ) {
	/**
	 * Twitter Settings Tab
	 * @param array $themify_theme_config
	 * @return array
	 */
	function themify_framework_theme_config_add_twitter_settings($themify_theme_config) {
		$themify_theme_config['panel']['settings']['tab']['twitter_settings'] = array(
			'title' => __('Twitter Settings', 'themify'),
			'id' => 'twitter_settings',
			'custom-module' => array(
				array(
					'title' => __('Twitter API Settings', 'themify'),
					'function' => 'themify_manage_twitter_settings'
				)
			)
		);
		return $themify_theme_config;
	};
	add_filter('themify_theme_config_setup', 'themify_framework_theme_config_add_twitter_settings');
}


if( ! function_exists( 'themify_framework_theme_config_add_script_minification ' ) ) {
	/**
	 * Script Minification
	 * @param array $themify_theme_config
	 * @return array
	 */
	function themify_framework_theme_config_add_script_minification($themify_theme_config) {
            $config = array();
            foreach($themify_theme_config['panel']['settings']['tab']['theme_settings']['custom-module'] as $index=>$val){
                    if($index===2){
                            $config[] =array(
                                'title' => __('Script Minification', 'themify'),
                                'function' => 'themify_manage_script_minification_settings'

                            );
                    }
                    $config[] = $val;
            }
            $themify_theme_config['panel']['settings']['tab']['theme_settings']['custom-module'] = $config;
            return $themify_theme_config;
	};
	add_filter('themify_theme_config_setup', 'themify_framework_theme_config_add_script_minification');
}
/**
 * Renders the option to disable responsive design
 *
 * @since 2.1.5
 * @return string
 */
function themify_disable_responsive_design_option( $data = array() ) {
	$out = '<p><label for="setting-disable_responsive_design"><input type="checkbox" id="setting-disable_responsive_design" name="setting-disable_responsive_design" ' . checked( themify_get( 'setting-disable_responsive_design' ), 'on', false ) . '/> ' . __( 'Check here to disable the responsive design.', 'themify' ) . '</label></p>';
        $out.='<div data-show-if-element="[name=setting-disable_responsive_design]" data-show-if-value="false">';
        $out .= sprintf( '<p class="clearfix"><span class="label width10">%s</span></p>', esc_html__( 'Customizer Responsive Breakpoints:', 'themify' ) );

	$opt_data = themify_get_data();
        $break_points = themify_get_breakpoints('',true);
	$pre = 'setting-customizer_responsive_design_';
	$bp_tablet_landscape = !empty( $opt_data[ $pre . 'tablet_landscape'] ) ? $opt_data[ $pre . 'tablet_landscape'] : $break_points['tablet_landscape'][1];
	$bp_tablet = !empty( $opt_data[ $pre . 'tablet'] ) ? $opt_data[ $pre . 'tablet'] : $break_points['tablet'][1];
	$bp_mobile =!empty( $opt_data[ $pre . 'mobile'] ) ? $opt_data[ $pre . 'mobile'] : $break_points['mobile'];
        
	$out .= sprintf( '<div class="clearfix"><div class="label">%s</div><div class="label input-range width10"><div class="range-slider width8"></div><input type="text" name="%s" value="%s" data-min="%d" data-max="%d" class="width4" readonly> px</div></div>',
		esc_html__( 'Tablet Landscape', 'themify' ),
		$pre . 'tablet_landscape',
		$bp_tablet_landscape,
		$break_points['tablet_landscape'][0],
		$break_points['tablet_landscape'][1],
		$bp_tablet_landscape
	);
	$out .= sprintf( '<div class="clearfix"><div class="label">%s</div><div class="label input-range width10"><div class="range-slider width8"></div><input type="text" name="%s" value="%s" data-min="%d" data-max="%d" class="width4" readonly> px</div></div>',
		esc_html__( 'Tablet', 'themify' ),
		$pre . 'tablet',
		$bp_tablet,
		$break_points['tablet'][0],
		$break_points['tablet'][1],
		$bp_tablet
	);
	$out .= sprintf( '<div class="clearfix"><div class="label">%s</div><div class="label input-range width10"><div class="range-slider width8"></div><input type="text" name="%s" value="%s" data-min="%d" data-max="%d" class="width4" readonly> px</div></div>',
		esc_html__( 'Mobile', 'themify' ),
		$pre . 'mobile',
		$bp_mobile,
		320,
		$break_points['mobile'],
		$bp_mobile
	);

	$point = themify_get( 'setting-mobile_menu_trigger_point', 900 );
	$out .= '
	<p>
		<span class="label">' . __( 'Mobile Menu', 'themify' ) . '</span>
		<input type="text" name="setting-mobile_menu_trigger_point" value="' . esc_attr( $point ) . '" class="width2">' . __( 'Mobile menu viewport (px)', 'themify' ) .'
		<small class="pushlabel">'. __( 'Main menu will toggle to mobile menu style when viewport width meets the entered value.', 'themify' ) .'</small>
	</p>';

	$out.='</div>';

	return $out;
}


if ( ! function_exists( 'themify_generic_slider_controls' ) ) {
	/**
	 * Creates a general module to setup slider parameters
	 * @param $prefix
	 * @return string
	 */
	function themify_generic_slider_controls( $prefix ) {
		/**
		 * Associative array containing theme settings
		 * @var array
		 */
		$data = themify_get_data();

		$auto_options = apply_filters( 'themify_generic_slider_auto',
			array(
				__('4 Secs (default)', 'themify') => 4000,
				__('Off', 'themify') => 'off',
				__('1 Sec', 'themify') => 1000,
				__('2 Secs', 'themify') => 2000,
				__('3 Secs', 'themify') => 3000,
				__('4 Secs', 'themify') => 4000,
				__('5 Secs', 'themify') => 5000,
				__('6 Secs', 'themify') => 6000,
				__('7 Secs', 'themify') => 7000,
				__('8 Secs', 'themify') => 8000,
				__('9 Secs', 'themify') => 9000,
				__('10 Secs', 'themify')=> 10000
			)
		);
		$speed_options = apply_filters( 'themify_generic_slider_speed',
			array(
				__('Fast', 'themify') => 500,
				__('Normal', 'themify') => 1000,
				__('Slow', 'themify') => 1500
			)
		);
		$effect_options = array(
			array('name' => __('Slide', 'themify'), 'value' => 'slide'),
			array('name' => __('Fade', 'themify'), 'value' =>'fade')
		);

		/**
		 * Auto Play
		 */
		$output = '<p>
						<span class="label">' . __('Auto Play', 'themify') . '</span>
						<select name="' . esc_attr( $prefix ) . 'autoplay">';
						foreach ( $auto_options as $name => $val ) {
							$output .= '<option value="' . esc_attr( $val ) . '" ' . selected( themify_get( $prefix . 'autoplay' ), themify_check( $prefix . 'autoplay' ) ? $val : 4000, false ) . '>' . esc_html( $name ) . '</option>';
						}
		$output .= '	</select>
					</p>';

		/**
		 * Effect
		 */
		$output .= '<p>
						<span class="label">' . __( 'Effect', 'themify' ) . '</span>
						<select name="' . esc_attr( $prefix ) . 'effect">' .
						themify_options_module( $effect_options, $prefix . 'effect' ) . '
						</select>
					</p>';

		/**
		 * Transition Speed
		 */
		$output .= '<p>
						<span class="label">' . __( 'Transition Speed', 'themify' ) . '</span>
						<select name="' . esc_attr( $prefix ) . 'transition_speed">';
						foreach ( $speed_options as $name => $val ) {
							$output .= '<option value="' . esc_attr( $val ) . '" ' . selected( themify_get( $prefix . 'transition_speed' ), themify_check( $prefix . 'transition_speed' ) ? $val : 500, false ) . '>' . esc_html( $name ) . '</option>';
						}
		$output .= '	</select>
					</p>';

		return apply_filters( 'themify_generic_slider_controls', $output );
	}
}

/**
 * Display select element with featured image sizes + blank slot
 * @param String $key setting name
 * @return String
 * @since 1.1.5
 */
function themify_feature_image_sizes_select($key = ''){
	/** Define WP Featured Image sizes + blank + Themify's image script
	 * @var array */
	$themify_layout_feature_sizes = themify_get_image_sizes_list();

	$output = '<p class="show_if_disabled_img_php">
				<span class="label">' . __('Featured Image Size', 'themify') . '</span>
				<select name="' . esc_attr( 'setting-' . $key ) . '">';
	foreach($themify_layout_feature_sizes as $option){
		if($option['value'] == themify_get('setting-'.$key.'')){
			$output .= '<option value="' . esc_attr( $option['value'] ) . '" selected="selected">';
				$output .= esc_html( $option['name'] );
			$output .= '</option>';
		} else {
			$output .= '<option value="' . esc_attr( $option['value'] ) . '">' . esc_html( $option['name'] ) . '</option>';
		}
	}
	$output .= '</select></p>';

	return $output;
}

if ( ! function_exists( 'themify_theme_mega_menu_controls' ) ) {
/**
 * Mega Menu Controls
 * @param array $data Theme settings data
 * @return string Markup for module.
 * @since 3.5.8
 */
function themify_theme_mega_menu_controls( $data = array() ) {
	/**
	 * Theme Settings Option Key Prefix
	 *
	 * @var string
	 */
	$key = 'setting-mega_menu';

	/**
	 * Module markup
	 * @var string
	 */
	 
	$mega = themify_get( $key.'_posts', 5 );
	$out = '
	<p>
		<span class="label">' . __( 'Mega Menu Posts', 'themify' ) . '</span>
		<input type="text" name="'.$key.'_posts" value="' . esc_attr( $mega ) . '" class="width2">' . __( 'Posts', 'themify' ) .'
		<br><small class="pushlabel">'. __( 'Number of posts to show on mega menu.', 'themify' ) .'</small>
	</p>';
	
	$width = themify_get( $key.'_image_width', 180 );
	$height = themify_get( $key.'_image_height', 120 );
	$out .= '
	<p>
		<span class="label">' . __( 'Mega Menu Posts', 'themify' ) . '</span>
		<input type="text" name="'.$key.'_image_width" value="' . esc_attr( $width ) . '" class="width2"> X <input type="text" name="'.$key.'_image_height" value="' . esc_attr( $height ) . '" class="width2"> ' . __( 'px', 'themify' ) .'
		<br><small class="pushlabel">'. __( 'Enter featured image size on mega menu', 'themify' ) .'</small>
	</p>';

	return $out;
}

}

// Add Google Map Api page
function themify_add_settings_page($themify_theme_config){
    	
        $themify_theme_config['panel']['settings']['tab']['google_map'] = array(
                'title' => __('Google Map', 'themify'),
                'id' => 'google_map',
                'custom-module' => array(
                        array(
							'title' => __('Google Map API Settings','themify'),
							'function' =>'themify_google_map_key'
                        )
                )
        );
    return $themify_theme_config;
}
add_filter('themify_theme_config_setup','themify_add_settings_page',11,1);

/**
 * Display google map api key input
 * @return String
 * @since 2.7.7
 */
function themify_google_map_key($data=array()){
    $google_map_key = themify_get( 'setting-google_map_key' );
    return '<p><span class="label">' . __( 'Google Map Key', 'themify' ) . '</span> <input type="text" class="width10" name="setting-google_map_key" value="' . esc_attr( $google_map_key ) . '" /> <br />
				<span class="pushlabel"><small>'.__('Google API key is required to use Builder Map module and Map shortcode.','themify').' <a href="//developers.google.com/maps/documentation/javascript/get-api-key#key" target="_blank">' . __( 'Generate an API key', 'themify' ) . '</a> and insert it here.</small></span></p>';
}

/**
 * Adds option to disable schema.org markup to Settings > General page
 *
 * @return array
 */
function themify_framework_theme_microdata_config( $themify_theme_config ) {
	$themify_theme_config['panel']['settings']['tab']['general']['custom-module'][] =
		array(
			'title' => __('Schema Microdata', 'themify'),
			'function' => 'themify_framework_theme_microdata_config_callback'
		)
	;
	return $themify_theme_config;
};
add_filter( 'themify_theme_config_setup', 'themify_framework_theme_microdata_config' );

/**
 * Callback for themify_framework_theme_microdata_config(), to display the options
 *
 * @return string
 */
function themify_framework_theme_microdata_config_callback() {
	return '<p><span class="label">' . __('Schema Microdata', 'themify') . '</span> <label for="setting-disable_microdata"><input type="checkbox" id="setting-disable_microdata" name="setting-disable_microdata" '. checked( 'on', themify_get( 'setting-disable_microdata' ), false ) .'/> ' . __('Disable schema.org microdata output.', 'themify') . '</label></p>';
}