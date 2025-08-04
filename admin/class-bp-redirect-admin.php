<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://wbcomdesigns.com/contact/
 * @since 1.0.0
 *
 * @package    BP_Redirect
 * @subpackage BP_Redirect/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    BP_Redirect
 * @subpackage BP_Redirect/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class BP_Redirect_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $bp_redirect    The ID of this plugin.
	 */
	private $bp_redirect;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Holds options key
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $options_key = 'bp_redirect_setting';

	/**
	 * Holds the plugin settings tabs.
	 *
	 * @var array
	 */
	protected $plugin_settings_tabs = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $bp_redirect The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $bp_redirect, $version ) {

		$this->bp_redirect = $bp_redirect;
		$this->version     = $version;
	}

	/**
	 * Define the plugin slug
	 *
	 * @var string $plugin_slug
	 */
	private $plugin_slug = 'bp-redirect';

	/**
	 * Builds plugin admin menu and pages
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */
	public function bp_redirect_admin_option() {
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page(
				esc_html__( 'WB Plugins', 'bp-redirect' ),
				esc_html__( 'WB Plugins', 'bp-redirect' ),
				'manage_options',
				'wbcomplugins',
				array( $this, 'bp_redirect_options_page' ),
				'dashicons-lightbulb',
				59
			);

			add_submenu_page(
				'wbcomplugins',
				esc_html__( 'General', 'bp-redirect' ),
				esc_html__( ' General ', 'bp-redirect' ),
				'manage_options',
				'wbcomplugins'
			);
		}
		add_submenu_page(
			'wbcomplugins',
			esc_html__( 'Redirect', 'bp-redirect' ),
			esc_html__( 'Redirect', 'bp-redirect' ),
			'manage_options',
			'bp-redirect',
			array( $this, 'bp_redirect_options_page' )
		);
	}

	/**
	 * Hide all notices from the setting page.
	 *
	 * @return void
	 */
	public function bp_redirect_hide_admin_notices_from_settings_page() {
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'bp-redirect' );
		// Get the 'page' parameter from the GET request and sanitize it
		$wbcom_setting_page = filter_input(INPUT_GET, 'page', FILTER_DEFAULT);

		if ($wbcom_setting_page !== null) {
			// Sanitize the input by removing any HTML tags and trimming whitespace
			$wbcom_setting_page = wp_strip_all_tags(trim($wbcom_setting_page));
		}

		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}


	/**
	 * Actions performed to create a submenu page content.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public function bp_redirect_options_page() {
		global $allowedposttags;
		$tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bp-redirect-welcome';
		?>
		<div class="wrap">
			<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
				</div>
			</div>
			<div class="wbcom-wrap bp-member-blog-wrap">
				<div class="blpro-header">
					<div class="wbcom_admin_header-wrapper">
						<div id="wb_admin_plugin_name">
							<?php esc_html_e( 'BuddyPress Redirect', 'bp-redirect' ); ?>
							<span><?php printf( esc_html__( 'Version %s', 'bp-redirect' ), esc_html( REDIRECT_PLUGIN_VERSION ) ); ?></span>
						</div>
						<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
					</div>
				</div>
				<?php settings_errors(); ?>
				<div class="wbcom-admin-settings-page">
					<?php
					$this->bp_redirect_settings_tabs();
					do_settings_sections( $tab );
					?>
				</div>
			</div>
		</div>
		<?php
	}


	/**
	 * Actions performed to create tabs on the sub menu page.
	 *
	 * @since    1.0.0
	 *
	 * @access public
	 */
	public function bp_redirect_settings_tabs() {
		$current_tab = filter_input( INPUT_GET, 'tab' ) ?? 'bp-redirect-welcome';
		echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html__( 'Menu', 'bp-redirect' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
			echo '<li class="' . esc_attr( $tab_key ) . '"><a class="nav-tab ' . esc_attr( $active ) . '" href="?page=' . esc_attr( $this->plugin_slug ) . '&tab=' . esc_attr( $tab_key ) . '">' . esc_html__( $tab_caption, 'bp-redirect' ) . '</a></li>';
		}
		echo '</ul></div></div>';
	}


	/**
	 * Actions performed on loading plugin settings
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bp_redirect_init_plugin_settings() {
		$this->plugin_settings_tabs['bp-redirect-welcome'] = esc_html__( 'Welcome', 'bp-redirect' );
		add_settings_section( 'bp-redirect-welcome-section', ' ', array( $this, 'bp_redirect_admin_welcome_content' ), 'bp-redirect-welcome' );

		$this->plugin_settings_tabs['bp-redirect-global'] = esc_html__( 'Global Redirection', 'bp-redirect' );
		add_settings_section( 'bp-redirect-global-section', ' ', array( $this, 'bp_redirect_admin_global_content' ), 'bp-redirect-global' );

		$this->plugin_settings_tabs['bp-redirect-role-settings'] = esc_html__( 'Redirect For User Role', 'bp-redirect' );
		register_setting( 'bp_redirect_role_admin_settings', 'bp_redirect_settings_role' );
		add_settings_section( 'bp-redirect-user-role', ' ', array( $this, 'bp_redirect_role_admin_settings_content' ), 'bp-redirect-role-settings' );

		if ( class_exists( 'BuddyPress' ) ) {
			$this->plugin_settings_tabs['bp-redirect-mem-type-settings'] = esc_html__( 'Redirect For Member Type', 'bp-redirect' );
			register_setting( 'bp_redirect_mem_type_admin_settings', 'bp_redirect_settings_mem_type' );
			add_settings_section( 'bp-redirect-member-type', ' ', array( $this, 'bp_redirect_mem_type_settings_content' ), 'bp-redirect-mem-type-settings' );
		}

		$this->plugin_settings_tabs['bp-redirect-faq'] = esc_html__( 'FAQ', 'bp-redirect' );
		register_setting( 'bp-redirect-faq', 'bp-redirect-faq' );
		add_settings_section( 'bp-redirect-faq-section', ' ', array( $this, 'bp_redirect_faq_content' ), 'bp-redirect-faq' );
	}

	/**
	 * Include buddypress multivender integration admin welcome setting tab content file.
	 */
	public function bp_redirect_admin_welcome_content() {
		include_once dirname( __FILE__ ) . '/partials/bp-redirect-welcome-page.php';
	}

	/**
	 * Include globel page setting
	 */
	public function bp_redirect_admin_global_content() {
		include_once dirname( __FILE__ ) . '/partials/bp-redirect-global-page.php';
	}

	/**
	 * This function is for that include admin option file
	 **/
	public function bp_redirect_role_admin_settings_content() {
		 include_once dirname( __FILE__ ) . '/partials/bp-redirect-user-role-tab.php';
	}


	/**
	 * This function is for that include admin option file
	 **/
	public function bp_redirect_mem_type_settings_content() {
		include_once dirname( __FILE__ ) . '/partials/bp-redirect-member-type-tab.php';
	}

	/**
	 * This function is for that include admin option file
	 **/
	public function bp_redirect_faq_content() {
		 include_once dirname( __FILE__ ) . '/partials/bp-redirect-faq.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		$admin_css = bp_redirect_get_asset_filename( 'admin/assets/css', 'bp-redirect-admin' );
		
		$plugin_setting = ( isset( $_GET['page'] ) && ( 'bp-redirect' === $_GET['page'] || 'wbcomplugins' === $_GET['page'] ) ) ? true : false; //phpcs:ignore
		
		if( $admin_css && $plugin_setting ) {
			wp_register_style( $this->bp_redirect, plugin_dir_url( __DIR__ ) . $admin_css, array(), $this->version, 'all' );
		
			wp_enqueue_style( $this->bp_redirect );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {		 

		if ( ! wp_script_is( 'jquery-ui-sortable', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
		}

		$admin_js = bp_redirect_get_asset_filename( 'admin/assets/js', 'bp-redirect-admin' );

		$plugin_setting = ( isset( $_GET['page'] ) && ( 'bp-redirect' === $_GET['page'] || 'wbcomplugins' === $_GET['page'] ) ) ? true : false; //phpcs:ignore

		if( $admin_js && $plugin_setting ) {
			
			wp_register_script( 'bp-redirect-admin-js', plugin_dir_url( __DIR__ ) . $admin_js, array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion' ), $this->version, false );

			wp_enqueue_script( 'bp-redirect-admin-js' );

			wp_localize_script(
				'bp-redirect-admin-js',
				'bp_redirect_ajax_nonce',
				array(
					'nonce' => wp_create_nonce( 'bp-js-admin-ajax-nonce' ),
				)
			);
		}
	}

	/**
	 * Get all roles
	 *
	 * This function is used for get roles.
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */
	public function bp_redirect_get_editable_roles() {
		global $wp_roles;

		$all_roles      = $wp_roles->roles;
		$editable_roles = apply_filters( 'editable_roles', $all_roles );

		return $editable_roles;
	}

	/**
	 *  Get all publish page id.
	 *
	 * @return [array] List of page IDs.
	 */
	public function bp_redirect_get_all_page_ids() {
		global $wpdb;

		$page_ids = wp_cache_get( 'all_page_ids', 'posts' );

		if ( ! is_array( $page_ids ) ) {
			$page_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_status = %s", 'page', 'publish' ) );
			wp_cache_add( 'all_page_ids', $page_ids, 'posts' );
		}
		
		return array_map( 'absint', $page_ids );
	}

	/**
	 * BP Login Redirect settings
	 *
	 * @param string $roles Get a user roles.
	 * @param string $bp_pages_ids Get Page id.
	 * @param string $saved_setting Login setting saved.
	 * @since  1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */
	public function bp_redirect_plugin_login_settings( $roles, $bp_pages_ids, $saved_setting ) {
		?>
		<div class="bpr-col-12">
			<form method="post" id="bpr-login-settings-form">
				<div id="bgr-login-accordion">
					<?php foreach ( $roles as $key => $val ) { ?>
						<div class="group" id="<?php echo esc_attr( 'login-' . $key ); ?>" data-text="<?php echo esc_attr( $key ); ?>">
							<h3><?php esc_html_e( $roles[ $key ]['name'], 'bp-redirect' ); ?></h3>
							<div>
								<?php
								$login_component = '';
								$login_url       = '';
								$login_type_val  = '';
								if ( ! empty( $saved_setting ) && isset( $saved_setting['bp_login_redirect_settings'] ) ) {
									$setting = $saved_setting['bp_login_redirect_settings'];
									
									if ( array_key_exists( $key, $setting ) ) {
										if ( array_key_exists( 'login_type', $setting[ $key ] ) ) {
											$login_type_val = $setting[ $key ]['login_type'];
										}
										if ( ! empty( $setting[ $key ]['login_component'] ) ) {
											$login_component = $setting[ $key ]['login_component'];
										}
										$login_url = $setting[ $key ]['login_url'];
									}
								}

								if ( is_multisite() ) {
									// Makes sure the plugin is defined before trying to use it.
									if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
										include_once ABSPATH . '/wp-admin/includes/plugin.php';
									}
									if ( is_plugin_active_for_network( 'buddypress/bp-loader.php' ) === true ) {
										?>
										<div class="bpr-col-4">
											<input name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_referer' ); ?>' value="referer" type="radio" class="bp_redi_login_type" 
											<?php checked( $login_type_val, 'referer' ); ?> >
											<label for="<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_referer' ); ?>"><?php esc_html_e( 'BuddyPress Component', 'bp-redirect' ); ?></label>
										</div>
										<?php
									}
								} elseif ( class_exists( 'BuddyPress' ) ) {
								
									?>
									<div class="bpr-col-4">
										<input name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_referer' ); ?>' value="referer" type="radio" class="bp_redi_login_type" 
										<?php checked( $login_type_val, 'referer' ); ?> >
										<label for="<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_referer' ); ?>"><?php esc_html_e( 'BuddyPress Component', 'bp-redirect' ); ?></label>
									</div>
									<?php
									
								}
								?>
								<div class="bpr-col-4">
									<input name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_custom' ); ?>' value="custom" type="radio" class="bp_redi_login_type" 
									<?php checked( $login_type_val, 'custom' ); ?> >
									<label for="<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_custom' ); ?>"><?php esc_html_e( 'Page', 'bp-redirect' ); ?></label>
								</div>
								<div class="bpr-col-4">
									<input name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_none' ); ?>' value="none" type="radio" class="bp_redi_login_type" 
									<?php checked( $login_type_val, 'none' ); ?> >
									<label for="<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_none' ); ?>"><?php esc_html_e( 'None', 'bp-redirect' ); ?></label>
								</div>
								<div class="bpr-col-4">
									<select name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_component]" ); ?>' class='bpr-login-component
										<?php
										if ( isset( $login_type_val ) && 'referer' === $login_type_val ) {
											echo 'bpr_show';
										}
										?> '>
										<?php if ( class_exists( 'BuddyPress' ) ) { ?>
											<option value=''><?php esc_html_e( 'Select', 'bp-redirect' ); ?></option>
											<?php if ( bp_is_active( 'members' ) ) { ?>
												<option value="profile" <?php selected( $login_component, 'profile' ); ?> >
													<?php esc_html_e( 'Member Profile', 'bp-redirect' ); ?>
												</option>
												<?php
											}
											if ( bp_is_active( 'activity' ) ) {
												?>
												<option value="member_activity" <?php selected( $login_component, 'member_activity' ); ?> >
													<?php esc_html_e( 'Member Activity', 'bp-redirect' ); ?>
												</option>
												<?php
											}
											if ( bp_is_active( 'groups' ) ) {
												?>
												<option value="groups" <?php selected( $login_component, 'groups' );?> >
													<?php esc_html_e( 'Groups', 'bp-redirect' ); ?>
												</option>
												<?php
											}
										}
										?>

										<?php
											if ( class_exists( 'BuddyPress' ) ) {
												$bp_pages = bp_core_get_directory_page_ids();
												$pages    = get_pages( array( 'include' => array_values( $bp_pages ) ) );
												foreach ( $pages as $page ) {
													if ( 'activity' === $login_component ) {	
														$selected = selected( $login_url, get_page_link( $page->ID ), false );												
														$option  = "<option value='" . esc_url( get_page_link( $page->ID ) ) . "' $selected>";
														$option .= esc_html( $page->post_title );
														$option .= '</option>';
														echo $option; //phpcs:ignore
													}
												}
											}
										?>
									</select>
								</div>
								<div class="bpr-col-4">
									
									<?php $wp_page_ids = $this->bp_redirect_get_all_page_ids(); ?>									
									<select name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_url]" ); ?>' class='bbr-login-<?php echo esc_attr( $key ); ?> bpr-login-custom <?php
										if ( isset( $login_type_val ) && 'custom' === $login_type_val ) {
											echo 'bpr_show';
										}
									?> ' data-text="<?php echo esc_attr( $key ); ?>" >
									<option value="" <?php selected( $login_url, '' ); ?> >
										<?php esc_html_e( 'Select Page', 'bp-redirect' )?>
									</option>
									
										<?php
											if ( $wp_page_ids ) {
												$page_url = array();
												foreach ( $wp_page_ids as $wp_page_id ) {
													$wp_page_url = get_permalink( $wp_page_id );
													$page_url[]  = $wp_page_url;
													?>
													<option value="<?php echo esc_url( $wp_page_url ); ?>" <?php selected( $login_url, $wp_page_url ); ?>>
														<?php echo esc_html( get_the_title( $wp_page_id ) ); ?>
													</option>
													<?php
												}
											}
										?>
										<option value=" <?php
											if ( ! empty( $login_url ) && ! in_array( $login_url, $page_url, true ) ) {
												echo esc_url( $login_url ); 
											}
											?>"  <?php selected( ! in_array( $login_url, $page_url, true ), true ); ?> > 
											<?php esc_html_e( 'Custom URL', 'bp-redirect' );?>
										</option>										
									</select>
								</div>
								<div class="bpr-col-4">
									<input type="url" name="custom-login-url" class="custom-login-url bbr-login-custom-<?php echo esc_attr( $key ); ?>" data-text="<?php echo esc_attr( $key ); ?>">
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</form>
		</div>
		<?php
	}


	/**
	 * BP Logout Redirect settings
	 *
	 * @param string $roles Get a user roles.
	 * @param string $bp_pages_ids Get Page id.
	 * @param string $saved_setting Logout setting saved.
	 * @since  1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */
	public function bp_redirect_plugin_logout_settings( $roles, $bp_pages_ids, $saved_setting ) {
		?>
		<div class="bpr-col-12">
			<form method="post" id="bpr-logout-settings-form">
				<div id="bgr-logout-accordion">
					<?php foreach ( $roles as $key => $val ) { ?>
						<div class="group" id="<?php echo esc_attr( 'logout-' . $key ); ?>" data-text="<?php echo esc_attr( $key ); ?>">
							<h3><?php esc_html_e( $roles[ $key ]['name'], 'bp-redirect' ); ?></h3>
							<div>
								<?php
								$logout_component = '';
								$logout_url       = '';
								$logout_type_val  = '';
								if ( ! empty( $saved_setting ) && isset( $saved_setting['bp_logout_redirect_settings'] ) ) {
									$setting = $saved_setting['bp_logout_redirect_settings'];
									if ( array_key_exists( $key, $setting ) ) {
										if ( array_key_exists( 'logout_type', $setting[ $key ] ) ) {
											$logout_type_val = $setting[ $key ]['logout_type'];
										}
										if ( ! empty( $setting[ $key ]['logout_component'] ) ) {
											$logout_component = $setting[ $key ]['logout_component'];
										}
										$logout_url = $setting[ $key ]['logout_url'];
									}
								}

								?>
								<div class="bpr-col-6">
									<input name='<?php echo esc_attr( "bp_logout_redirect_settings[$key][logout_type]" ); ?>' id='<?php echo esc_attr( 'bp_logout_redirect_settings_' . $key . '_logout_type_custom' ); ?>' value="custom" type="radio" class="bp_redi_logout_type" 
									<?php checked( $logout_type_val, 'custom' ); ?> >
									<label for="<?php echo esc_attr( 'bp_logout_redirect_settings_' . $key . '_logout_type_custom' ); ?>"><?php esc_html_e( 'Page', 'bp-redirect' ); ?></label>
								</div>
								<div class="bpr-col-6">
									<input name='<?php echo esc_attr( "bp_logout_redirect_settings[$key][logout_type]" ); ?>' id='<?php echo esc_attr( 'bp_logout_redirect_settings_' . $key . '_logout_type_none' ); ?>' value="none" type="radio" class="bp_redi_logout_type" 
									<?php checked( $logout_type_val, 'none' ); ?> >
									<label for="<?php echo esc_attr( 'bp_logout_redirect_settings_' . $key . '_logout_type_none' ); ?>"><?php esc_html_e( 'None', 'bp-redirect' ); ?></label>
								</div>

								<div class="bpr-col-6">
									<?php $wp_page_ids = $this->bp_redirect_get_all_page_ids(); ?>
									<select name='<?php echo esc_attr( "bp_logout_redirect_settings[$key][logout_url]" ); ?>' class="bbr-logout-<?php echo esc_attr( $key ); ?> bpr-logout-custom
									<?php
									if ( isset( $logout_type_val ) && 'custom' === $logout_type_val ) {
										echo 'bpr_show';
									}
									?> " data-text="<?php echo esc_attr( $key ); ?>" >
									<option value="" <?php selected( $logout_url, '' ); ?> > 
										<?php esc_html_e( 'Select Page', 'bp-redirect' ); ?>
									</option>
									<?php
										if ( $wp_page_ids ) {
											$page_url = array();
											foreach ( $wp_page_ids as $wp_page_id ) {
												$wp_page_url = get_permalink( $wp_page_id );
												$page_url[]  = $wp_page_url;
												?>
												<option value="<?php echo esc_url( $wp_page_url ); ?>" <?php selected( $logout_url, $wp_page_url ); ?>>
													<?php echo esc_html_e( get_the_title( $wp_page_id ), 'bp-redirect' ); ?>
												</option>
												<?php
											}
										}
										?>
									<option value="
										<?php if ( ! empty( $logout_url ) && ! in_array( $logout_url, $page_url, true ) ) 
										{
											echo esc_url( $logout_url ); 
										}
										?>" 
										<?php selected( ! in_array( $logout_url, $page_url, true ), true ); ?> >
										<?php esc_html_e( 'Custom URL', 'bp-redirect' );?>
									</option>
									</select>
								</div>
								<div class="bpr-col-6">
									<input type="url" name="custom-logout-url" class="custom-logout-url bbr-logout-custom-<?php echo esc_attr( $key ); ?>" data-text="<?php echo esc_attr( $key ); ?>">
								</div>
							</div>
						</div>
					<?php } ?>
				</div>

			</form>
		</div>
		<?php
	}




	/**
	 * BP Login Redirect settings
	 *
	 * @param string $roles Get a user roles.
	 * @param string $bp_pages_ids Get Page id.
	 * @param string $saved_setting Login setting saved.
	 * @since  1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */
	public function bp_redirect_plugin_global_login_settings( $roles, $bp_pages_ids, $saved_setting ) {
		?>
		<div class="bpr-col-12">
			<form method="post" id="bpr-login-settings-form-global">
				<div id="bgr-login-accordion">
					<?php
					//foreach ( $roles as $key => $val ) {
						$key = 'global';
					?>
					<div class="group" id="<?php echo esc_attr( 'login' ); ?>" data-text="<?php echo esc_attr( $key ); ?>">
						<h3><?php esc_html_e( 'Login Redirect Settings For All Roles', 'bp-redirect' ); ?></h3>
						<div>
							<?php
							$login_component = '';
							$login_url       = '';
							$login_type_val  = '';

							if ( ! empty( $saved_setting ) && isset( $saved_setting['bp_login_redirect_settings_global'] ) ) {
								if ( ! empty( $saved_setting['bp_login_redirect_settings_global'] ) ) {
									$setting = $saved_setting['bp_login_redirect_settings_global'];

									if ( isset( $setting ) && ! empty( $setting ) ) {
										if ( array_key_exists( $key, $setting ) ) {
											if ( array_key_exists( 'login_type', $setting[ $key ] ) ) {
												$login_type_val = $setting[ $key ]['login_type'];
											}
											if ( ! empty( $setting[ $key ]['login_component'] ) ) {
												$login_component = $setting[ $key ]['login_component'];
											}
											$login_url = $setting[ $key ]['login_url'];
										}
									}
								}
							}

							if ( is_multisite() ) {
								// Makes sure the plugin is defined before trying to use it.
								if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
									include_once ABSPATH . '/wp-admin/includes/plugin.php';
								}
								if ( class_exists( 'BuddyPress' ) ) {
									?>
									<div class="bpr-col-4">
										<input name='<?php echo esc_attr( "bp_login_redirect_settings_global[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_global_' . $key . '_login_type_referer' ); ?>' value="referer" type="radio" class="bp_redi_login_type" <?php checked( $login_type_val, 'referer' ) ; ?> >
										<label for="<?php echo esc_attr( 'bp_login_redirect_settings_global_' . $key . '_login_type_referer' ); ?>"><?php esc_html_e( 'BuddyPress Component', 'bp-redirect' ); ?></label>
									</div>
									<?php
								}
							} elseif ( class_exists( 'BuddyPress' ) ) {
								
								?>
								<div class="bpr-col-4">
									<input name='<?php echo esc_attr( "bp_login_redirect_settings_global[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_global_' . $key . '_login_type_referer' ); ?>' value="referer" type="radio" class="bp_redi_login_type" <?php checked( $login_type_val, 'referer' ); ?> >

									<label for="<?php echo esc_attr( 'bp_login_redirect_settings_global_' . $key . '_login_type_referer' ); ?>"><?php esc_html_e( 'BuddyPress Component', 'bp-redirect' ); ?></label>
								</div>
								<?php
								
							}
							?>
							<div class="bpr-col-4">

								<input name='<?php echo esc_attr( "bp_login_redirect_settings_global[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_global_' . $key . '_login_type_custom' ); ?>' value="custom" type="radio" class="bp_redi_login_type" 
								<?php checked( $login_type_val, 'custom' ); ?> >
								<label for="<?php echo esc_attr( 'bp_login_redirect_settings_global_' . $key . '_login_type_custom' ); ?>"><?php esc_html_e( 'Page', 'bp-redirect' ); ?></label>
							</div>
							<div class="bpr-col-4">
								<input name='<?php echo esc_attr( "bp_login_redirect_settings_global[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_global_' . $key . '_login_type_none' ); ?>' value="none" type="radio" class="bp_redi_login_type" 
								<?php checked( $login_type_val, 'none' ); ?> >
								<label for="<?php echo esc_attr( 'bp_login_redirect_settings_global_' . $key . '_login_type_none' ); ?>"><?php esc_html_e( 'None', 'bp-redirect' ); ?></label>
							</div>
							<div class="bpr-col-4">
								<select name='<?php echo esc_attr( "bp_login_redirect_settings_global[$key][login_component]" ); ?>' class='bpr-login-component
									<?php
										if ( isset( $login_type_val ) && 'referer' === $login_type_val ) {
											echo 'bpr_show';
										}
									?> ' >
									
									<?php if ( class_exists( 'BuddyPress' ) ) { ?>
										<option value=''><?php esc_html_e( 'Select', 'bp-redirect' ); ?></option>
										<?php if ( bp_is_active( 'members' ) ) { ?>
											<option value="profile" <?php selected( $login_component, 'profile' ); ?>> 
												<?php esc_html_e( 'Member Profile', 'bp-redirect' ); ?>
											</option>
											<?php
										}
										if ( bp_is_active( 'activity' ) ) {
											?>
											<option value="member_activity" <?php selected( $login_component, 'member_activity' ); ?> > 
												<?php esc_html_e( 'Member Activity', 'bp-redirect' ); ?>
											</option>
											<?php
										}
										if ( bp_is_active( 'groups' ) ) {
											?>
											<option value="groups" <?php selected( $login_component, 'groups' ); ?> >
												<?php esc_html_e( 'Groups', 'bp-redirect' ); ?>
											</option>
											<?php
										}
									}
									?>
									
									<?php

									if ( class_exists( 'BuddyPress' ) ) {
										$bp_pages = bp_core_get_directory_page_ids();
										$pages    = get_pages( array( 'include' => $bp_pages ) );
										foreach ( $pages as $page ) {

											if ( 'activity' === $login_component ) {
												$option  = "<option value='" . get_page_link( $page->ID ) . selected( $login_url, get_page_link( $page->ID ) )."'>";
												$option .= $page->post_title;
												$option .= '</option>';
												echo $option; //phpcs:ignore
											}
										}
									} 
									?>
								</select>
							</div>
							<div class="bpr-col-4">
								<?php
								$wp_page_ids = $this->bp_redirect_get_all_page_ids();

								?>
								<select name='<?php echo esc_attr( "bp_login_redirect_settings_global[$key][login_url]" ); ?>' class='bbr-login-<?php echo esc_attr( $key ); ?> bpr-login-custom
									<?php
									if ( isset( $login_type_val ) && 'custom' === $login_type_val ) {
										echo 'bpr_show';
									}
									?> ' data-text="<?php echo esc_attr( $key ); ?>">								 
									<option value="" <?php selected( $login_url, '' ); ?>>
										<?php esc_html_e( 'Select Page', 'bp-redirect' )?> 
									</option>
									<?php
									if ( $wp_page_ids ) {
										$page_url = array();
										foreach ( $wp_page_ids as $wp_page_id ) {
											$wp_page_url = get_permalink( $wp_page_id );
											$page_url[]  = $wp_page_url;
											?>
											<option value="<?php echo esc_url( $wp_page_url ); ?>" <?php selected( $login_url, $wp_page_url ); ?>>
												<?php echo esc_html( get_the_title( $wp_page_id ) ); ?>
											</option>
											<?php
										}
									}

									?>
									<option value=" <?php
										if ( ! empty( $login_url ) && ! in_array( $login_url, $page_url, true ) ) {
											echo esc_url( $login_url ); 
										} ?> " 
										<?php selected( ! in_array( $login_url, $page_url, true ), true ); ?> >
										<?php esc_html_e( 'Custom URL', 'bp-redirect' ); ?> 
									</option>
								</select>
								
							</div>
							<div class="bpr-col-4">
								<input type="url" name="custom-login-url" class="custom-login-url bbr-login-custom-<?php echo esc_attr( $key ); ?>" data-text="<?php echo esc_attr( $key ); ?>">
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * BP Logout Redirect settings
	 *
	 * @param string $roles Get a user roles.
	 * @param string $bp_pages_ids Get Page id.
	 * @param string $saved_setting Logout setting saved.
	 * @since  1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */
	public function bp_redirect_plugin_global_logout_settings( $roles, $bp_pages_ids, $saved_setting ) {
		$key = 'global';
		?>
		<div class="bpr-col-12">
			<form method="post" id="bpr-logout-settings-form">
				<div id="bgr-logout-accordion">
					<?php
					//foreach ($roles as $key => $val) {
					?>
					<div class="group" id="<?php echo esc_attr( 'logout-' . $key ); ?>" data-text="<?php echo esc_attr( $key ); ?>">
						<h3><?php esc_html_e( 'Logout Redirect Settings For All Roles', 'bp-redirect' ); ?></h3>
						<div>
							<?php
							$logout_component = '';
							$logout_url       = '';
							$logout_type_val  = '';

							if ( ! empty( $saved_setting ) && isset( $saved_setting['bp_logout_redirect_settings_global'] ) ) {
								if ( ! empty( $saved_setting['bp_logout_redirect_settings_global'] ) ) {
									$setting = $saved_setting['bp_logout_redirect_settings_global'];
									if ( isset( $setting ) && ! empty( $setting ) ) {
										if ( array_key_exists( $key, $setting ) ) {
											if ( array_key_exists( 'logout_type', $setting[ $key ] ) ) {
												$logout_type_val = $setting[ $key ]['logout_type'];
											}
											if ( ! empty( $setting[ $key ]['logout_component'] ) ) {
												$logout_component = $setting[ $key ]['logout_component'];
											}
											$logout_url = $setting[ $key ]['logout_url'];
										}
									}
								}
							}
							?>
							<div class="bpr-col-6">
								<input name='<?php echo esc_attr( "bp_logout_redirect_settings_global[$key][logout_type]" ); ?>' id='<?php echo esc_attr( 'bp_logout_redirect_settings_' . $key . '_logout_type_custom' ); ?>' value="custom" type="radio" class="bp_redi_logout_type" 
								<?php checked( $logout_type_val, 'custom' ); ?> >
								<label for="<?php echo esc_attr( 'bp_logout_redirect_settings_global_' . $key . '_logout_type_custom' ); ?>"><?php esc_html_e( 'Page', 'bp-redirect' ); ?></label>
							</div>
							<div class="bpr-col-6">
								<input name='<?php echo esc_attr( "bp_logout_redirect_settings_global[$key][logout_type]" ); ?>' id='<?php echo esc_attr( 'bp_logout_redirect_settings_' . $key . '_logout_type_none' ); ?>' value="none" type="radio" class="bp_redi_logout_type" 
								<?php checked( $logout_type_val, 'none' ) ; ?> >
								<label for="<?php echo esc_attr( 'bp_logout_redirect_settings_global_' . $key . '_logout_type_none' ); ?>"><?php esc_html_e( 'None', 'bp-redirect' ); ?></label>
							</div>

							<div class="bpr-col-6">
								<?php $wp_page_ids = $this->bp_redirect_get_all_page_ids(); ?>
								<select name='<?php echo esc_attr( "bp_logout_redirect_settings_global[$key][logout_url]" ); ?>' class="bbr-logout-<?php echo esc_attr( $key ); ?> bpr-logout-custom
									<?php
									if ( isset( $logout_type_val ) && 'custom' === $logout_type_val ) {
										echo 'bpr_show';
									}
									?> " data-text="<?php echo esc_attr( $key ); ?>" >
												
									<option value="" <?php selected( $logout_url, '' ); ?> > 
										<?php esc_html_e( 'Select Page', 'bp-redirect' ); ?></option>
									<?php
									if ( $wp_page_ids ) {
										$page_url = array();
										foreach ( $wp_page_ids as $wp_page_id ) {
											$wp_page_url = get_permalink( $wp_page_id );
											$page_url[]  = $wp_page_url;
											?>
											<option value="<?php echo esc_attr( $wp_page_url ); ?>" <?php selected( $logout_url, $wp_page_url ); ?>>
												<?php echo esc_html_e( get_the_title( $wp_page_id ), 'bp-redirect' ); ?>
											</option>
											<?php
										}
									}
									?>
									<option value="
										<?php
										if ( ! empty( $logout_url ) && ! in_array( $logout_url, $page_url, true ) ) {
											echo esc_url( $logout_url ); 
										}
										?> " <?php selected( ! in_array( $logout_url, $page_url, true ), true ); ?> > 
										<?php esc_html_e( 'Custom URL', 'bp-redirect' ); ?> 
									</option>
								</select>
							</div>
							<div class="bpr-col-6">
								<input type="url" name="custom-logout-url" class="custom-logout-url bbr-logout-custom-<?php echo esc_attr( $key ); ?>" data-text="<?php echo esc_attr( $key ); ?>">
							</div>
						</div>
					</div>
				</div>

			</form>
		</div>
		<?php
	}



	/**
	 * Actions performed for saving admin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function bp_redirect_save_admin_settings() {		
		if ( isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'bp-js-admin-ajax-nonce') ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( isset($_POST['action']) && 'bp_redirect_admin_settings' === $_POST['action'] ) {
				// Retrieve the existing settings
				if ( isset( $_POST['temp_role_member_type'] ) && 'user-role' === $_POST['temp_role_member_type'] ){
					$saved_setting = get_option('bp_redirect_admin_settings');			
				} elseif( isset( $_POST['temp_role_member_type'] ) && 'member-type' === $_POST['temp_role_member_type'] ){
					$saved_setting = get_option('bp_redirect_member_type_admin_settings');			
				}
				
				// Parse the incoming form data
				parse_str(wp_unslash(filter_input(INPUT_POST, 'login_details')), $login_form_data);
				parse_str(wp_unslash(filter_input(INPUT_POST, 'logout_details')), $logout_form_data);

				// Ensure these variables are arrays
				$login_details  = is_array($login_form_data) ? $login_form_data : [];
				$logout_details = is_array($logout_form_data) ? $logout_form_data : [];
				
				// Ensure the saved settings are arrays
				if (!is_array($saved_setting)) {
					$saved_setting = [
						'bp_login_redirect_settings' => [],
						'bp_logout_redirect_settings' => [],
					];
				}

				// Get all roles and member types (including custom ones)				
				if ( isset( $_POST['temp_role_member_type'] ) && 'user-role' === $_POST['temp_role_member_type'] ) {
					$all_keys = array_keys(wp_roles()->roles); // WordPress roles					
				} else {
					$all_keys = function_exists('bp_get_member_types') ? bp_get_member_types() : []; // BuddyPress member types					
				}

				// Initialize missing keys with default values
				foreach ($all_keys as $key) {										
					if ( ! isset($saved_setting['bp_login_redirect_settings'][$key])) {
						$saved_setting['bp_login_redirect_settings'][$key] = [
							'login_type' => 'none',
							'login_component' => '',
							'login_url' => '',
						];
					}

					if ( ! isset($saved_setting['bp_logout_redirect_settings'][$key])) {
						$saved_setting['bp_logout_redirect_settings'][$key] = [
							'logout_url' => '',
						];
					}
				}

				// Process login details
				foreach ($login_details['bp_login_redirect_settings'] as $key => $lgn_detail) {
					// Ensure login_type exists
					if ( ! isset($lgn_detail['login_type'])) {
						$lgn_detail['login_type'] = 'none';
					}
					if( $lgn_detail['login_type'] == 'none' ){
						$lgn_detail['login_component'] = '';
						$lgn_detail['login_url'] = '';
					}

					if( $lgn_detail['login_type'] == 'referer' ){
						$lgn_detail['login_url'] = '';
					}

					if( $lgn_detail['login_type'] == 'custom' ){
						$lgn_detail['login_component'] = '';
					}
					
					$saved_setting['bp_login_redirect_settings'][$key] = $lgn_detail;
				}

				// Process logout details
				foreach ( $logout_details['bp_logout_redirect_settings'] as $key => $lgt_detail ) {
					if ( ! isset($lgt_detail['logout_type'])) {
						$lgt_detail['logout_type'] = 'none';
					}
					if( $lgt_detail['logout_type'] == 'none' ){
						$lgt_detail['logout_url'] = '';
					}

					$saved_setting['bp_logout_redirect_settings'][$key] = $lgt_detail;
				}

				// Process additional settings
				if (isset($_POST['enable_disable_setting']) && '' !== $_POST['enable_disable_setting']) {
					$saved_setting['member_type_btn_value'] = sanitize_text_field(wp_unslash($_POST['enable_disable_setting']));
				}

				if (isset($_POST['enable_disable_role_setting']) && '' !== $_POST['enable_disable_role_setting']) {
					$saved_setting['role_btn_value'] = sanitize_text_field(wp_unslash($_POST['enable_disable_role_setting']));
				}

				if (isset($_POST['loginSequence']) && '' !== $_POST['loginSequence']) {
					$saved_setting['loginSequence'] = sanitize_text_field(wp_unslash($_POST['loginSequence']));
				}

				if (isset($_POST['logoutSequence']) && '' !== $_POST['logoutSequence']) {
					$saved_setting['logoutSequence'] = sanitize_text_field(wp_unslash($_POST['logoutSequence']));
				}
				if ( isset( $_POST['temp_role_member_type'] ) && 'user-role' == $_POST['temp_role_member_type'] ){
					// Save the user role settings back to the database
					update_option('bp_redirect_admin_settings', $saved_setting);
				} elseif( isset( $_POST['temp_role_member_type'] ) && 'member-type' == $_POST['temp_role_member_type'] ){
					// Save the member type settings back to the database
					update_option('bp_redirect_member_type_admin_settings', $saved_setting);
				}
			}
		}
		exit;
	}


	function bp_redirect_save_admin_settings_global() {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bp-js-admin-ajax-nonce' ) ) {
			if ( isset( $_POST['action'] ) && 'bp_redirect_admin_settings_global' === $_POST['action'] ) {
				
				$saved_setting = get_option( 'bp_redirect_admin_settings_global' );

				parse_str( wp_unslash( filter_input( INPUT_POST, 'login_details', FILTER_UNSAFE_RAW ) ), $login_form_data );
				parse_str( wp_unslash( filter_input( INPUT_POST, 'logout_details', FILTER_UNSAFE_RAW ) ), $logout_form_data );
				$login_details     = filter_var_array( $login_form_data, FILTER_UNSAFE_RAW );
				$logout_details    = filter_var_array( $logout_form_data, FILTER_UNSAFE_RAW );
				$login_array_keys  = array();
				$logout_array_keys = array();
				if ( ! empty( $saved_setting ) && isset( $saved_setting['bp_login_redirect_settings_global'] ) && isset( $saved_setting['bp_logout_redirect_settings_global'] ) ) {
					$login_array_keys  = array_keys( $saved_setting['bp_login_redirect_settings_global'] );
					$logout_array_keys = array_keys( $saved_setting['bp_logout_redirect_settings_global'] );
				} else {
					$saved_setting = array();
				}
				foreach ( $login_details['bp_login_redirect_settings_global'] as $key => $lgn_detail ) {
					if ( in_array( $key, $login_array_keys, true ) ) {
						unset( $saved_setting['bp_login_redirect_settings_global'][ $key ] );
						$saved_setting['bp_login_redirect_settings_global'][ $key ] = $lgn_detail;
					} else {
						$saved_setting['bp_login_redirect_settings_global'][ $key ] = $lgn_detail;
					}
					
					if( isset( $lgn_detail['login_type'] ) && $lgn_detail['login_type'] == 'none' ){						
						$lgn_detail['login_component'] = '';
						$lgn_detail['login_url'] = '';
					}

					if( isset( $lgn_detail['login_type'] ) && $lgn_detail['login_type'] == 'referer' ){
						$lgn_detail['login_url'] = '';
					}

					if( isset( $lgn_detail['login_type'] ) && $lgn_detail['login_type'] == 'custom' ){
						$lgn_detail['login_component'] = '';
					}
					$saved_setting['bp_login_redirect_settings_global'][ $key ] = $lgn_detail;
				}
				foreach ( $logout_details['bp_logout_redirect_settings_global'] as $key => $lgt_detail ) {
					if ( in_array( $key, $logout_array_keys, true ) ) {
						unset( $saved_setting['bp_logout_redirect_settings_global'][ $key ] );
						$saved_setting['bp_logout_redirect_settings_global'][ $key ] = $lgt_detail;
					} else {
						$saved_setting['bp_logout_redirect_settings_global'][ $key ] = $lgt_detail;
					}
					if( isset( $lgt_detail['logout_type'] ) && $lgt_detail['logout_type'] == 'none' ){
						$lgt_detail['logout_url'] = '';
					}
					$saved_setting['bp_logout_redirect_settings_global'][ $key ] = $lgt_detail;
				}
				if ( isset( $_POST['enable_disable_setting'] ) && '' !== $_POST['enable_disable_setting'] ) {
					$saved_setting['member_type_btn_value'] = sanitize_text_field( wp_unslash( $_POST['enable_disable_setting'] ) );
				}
				if ( isset( $_POST['enable_disable_role_setting'] ) && '' !== $_POST['enable_disable_role_setting'] ) {
					$saved_setting['role_btn_value'] = sanitize_text_field( wp_unslash( $_POST['enable_disable_role_setting'] ) );
				}
				if ( isset( $_POST['loginSequence'] ) && '' !== $_POST['loginSequence'] ) {
					$saved_setting['loginSequence'] = sanitize_text_field( wp_unslash( $_POST['loginSequence'] ) );
				}
				if ( isset( $_POST['logoutSequence'] ) && '' !== $_POST['logoutSequence'] ) {
					$saved_setting['logoutSequence'] = sanitize_text_field( wp_unslash( $_POST['logoutSequence'] ) );
				}
				
				update_option( 'bp_redirect_admin_settings_global', $saved_setting );
				
			}
		}
		exit;
	}
}
