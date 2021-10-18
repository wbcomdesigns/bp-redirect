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
			esc_html__( 'BP Redirect', 'bp-redirect' ),
			esc_html__( 'BP Redirect', 'bp-redirect' ),
			'manage_options',
			'bp-redirect',
			array( $this, 'bp_redirect_options_page' )
		);
	}



	/**
	 * Actions performed to create a submenu page content.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public function bp_redirect_options_page() {
		global $allowedposttags;
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'bp-redirect-welcome';
		?>
		<div class="wrap">
					<hr class="wp-header-end">
					<div class="wbcom-wrap">
			<div class="lrc-header">
		<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				<h1 class="wbcom-plugin-heading">
		<?php esc_html_e( 'Bp Redirect Settings', 'bp-redirect' ); ?>
				</h1>
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
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'bp-redirect-welcome';
		echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
			echo '<li><a class="nav-tab ' . esc_attr( $active ) . '" href="?page=' . esc_attr( $this->plugin_slug ) . '&tab=' . esc_attr( $tab_key ) . '">' . esc_html__( $tab_caption, 'bp-redirect' ) . '</a></li>';
		}
		echo '</div></ul></div>';
	}


	/**
	 * Actions performed on loading plugin settings
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bp_redirect_init_plugin_settings() {
		$this->plugin_settings_tabs['bp-redirect-welcome'] = __( 'Welcome', 'bp-redirect' );
		add_settings_section( 'bp-redirect-welcome-section', ' ', array( $this, 'bp_redirect_admin_welcome_content' ), 'bp-redirect-welcome' );
		$this->plugin_settings_tabs['bp-redirect-role-settings'] = __( 'Redirect For User Role', 'bp-redirect' );
		register_setting( 'bp_redirect_role_admin_settings', 'bp_redirect_settings_role' );
		add_settings_section( 'bp-redirect-role', ' ', array( $this, 'bp_redirect_role_admin_settings_content' ), 'bp-redirect-role-settings' );
		$this->plugin_settings_tabs['bp-redirect-mem-type-settings'] = __( 'Redirect For Member Type', 'bp-redirect' );
		register_setting( 'bp_redirect_mem_type_admin_settings', 'bp_redirect_settings_mem_type' );
		add_settings_section( 'bp-redirect-role', ' ', array( $this, 'bp_redirect_mem_type_settings_content' ), 'bp-redirect-mem-type-settings' );
		
                $this->plugin_settings_tabs['bp-redirect-faq'] = __( 'FAQ', 'bp-redirect' );
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

		wp_enqueue_style( $this->bp_redirect, plugin_dir_url( __FILE__ ) . 'assets/css/bp-redirect-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'bp-redirect-admin', plugin_dir_url( __FILE__ ) . 'assets/js/bp-redirect-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			'bp-redirect-admin',
			'bp_redirect_ajax_nonce',
			array(
				'nonce' => wp_create_nonce( 'bp-js-admin-ajax-nonce' ),
			)
		);
		if ( ! wp_script_is( 'jquery-ui-sortable', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
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
	public function get_editable_roles() {
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
			$page_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish'" );
			wp_cache_add( 'all_page_ids', $page_ids, 'posts' );
		}

		return $page_ids;
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
						<div class="group" id="<?php echo esc_attr( 'login-' . $key ); ?>" >
							<h3><?php esc_html_e( $roles[ $key ]['name'], 'bp-redirect' ); ?></h3>
							<div>
			<?php
			$login_component = '';
			$login_url       = '';
			if ( ! empty( $saved_setting ) ) {
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
											<input name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_type]" ); ?>' id= '<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_referer' ); ?>' value="referer" type="radio" class="bp_redi_login_type"
					<?php
					if ( isset( $login_type_val ) && 'referer' === $login_type_val ) {
						echo "checked = 'checked'";
					}
					?>
											>
											<label for="<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_referer' ); ?>"><?php esc_html_e( 'BuddyPress Component', 'bp-redirect' ); ?></label>
										</div>
					<?php
				}
			} elseif ( in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', bp_get_option( 'active_plugins' ) ) ) ) {
				?>
								<div class="bpr-col-4">
									<input name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_type]" ); ?>' id= '<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_referer' ); ?>' value="referer" type="radio" class="bp_redi_login_type"
				<?php
				if ( isset( $login_type_val ) && 'referer' === $login_type_val ) {
					echo "checked = 'checked'";
				}
				?>
									>
									<label for="<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_referer' ); ?>"><?php esc_html_e( 'BuddyPress Component', 'bp-redirect' ); ?></label>
								</div>
			<?php } ?>
								<div class="bpr-col-4">
									<input name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_custom' ); ?>' value="custom" type="radio" class="bp_redi_login_type"
															<?php
															if ( isset( $login_type_val ) && 'custom' === $login_type_val ) {
																echo "checked = 'checked'";
															}
															?>
									>
									<label for="<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_custom' ); ?>"><?php esc_html_e( 'Page', 'bp-redirect' ); ?></label>
								</div>
								<div class="bpr-col-4">
									<input name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_type]" ); ?>' id='<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_none' ); ?>' value="none" type="radio" class="bp_redi_login_type"
															<?php
															if ( isset( $login_type_val ) && 'none' === $login_type_val ) {
																echo "checked = 'checked'";
															}
															?>
									>
									<label for="<?php echo esc_attr( 'bp_login_redirect_settings_' . $key . '_login_type_none' ); ?>"><?php esc_html_e( 'None', 'bp-redirect' ); ?></label>
								</div>
								<div class="bpr-col-4">
									<select name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_component]" ); ?>' class='bpr-login-component
															<?php
															if ( isset( $login_type_val ) && 'referer' === $login_type_val ) {
																echo 'bpr_show';
															}
															?>
									' >
									<option value=''><?php esc_html_e( 'Select', 'bp-redirect' ); ?></option>
			<?php if ( bp_is_active( 'members' ) ) { ?>
												<option value= "profile"
																		<?php
																		if ( 'profile' === $login_component ) {
																			echo "selected = 'selected'";
																		}
																		?>
												><?php esc_html_e( 'Member Profile', 'bp-redirect' ); ?>
												</option>
			<?php	} if ( bp_is_active( 'activity' ) ) { ?>
												<option value= "member_activity"
																		<?php
																		if ( 'member_activity' === $login_component ) {
																			echo "selected = 'selected'";
																		}
																		?>
												><?php esc_html_e( 'Member Activity', 'bp-redirect' ); ?>
												</option>
				<?php
			} if ( bp_is_active( 'groups' ) ) {
				?>
						<option value= "groups"
																		<?php
																		if ( 'groups' === $login_component ) {
																			echo "selected = 'selected'";
																		}
																		?>
												><?php esc_html_e( 'Groups', 'bp-redirect' ); ?>
												</option>
				<?php
			}
			?>

			<?php
										$bp_pages = bp_core_get_directory_page_ids();
										$pages    = get_pages( array( 'include' => $bp_pages ) );
			foreach ( $pages as $page ) {
				if ( 'Activity' === $page->post_title ) {
					$option  = '<option value="' . get_page_link( $page->ID ) . '">';
					$option .= $page->post_title;
					$option .= '</option>';
					echo $option;
				}
			}
			?>
									</select>
								</div>
								<div class="bpr-col-4">
			<?php $wp_page_ids = $this->bp_redirect_get_all_page_ids(); ?>
									<select name='<?php echo esc_attr( "bp_login_redirect_settings[$key][login_url]" ); ?>' class='bpr-login-custom
															<?php
															if ( isset( $login_type_val ) && 'custom' === $login_type_val ) {
																echo 'bpr_show';
															}
															?>
									' >
			<?php
			if ( $wp_page_ids ) {
				foreach ( $wp_page_ids as $wp_page_id ) {
					$wp_page_url = get_permalink( $wp_page_id );
					?>
												<option value="<?php echo $wp_page_url; ?>" <?php selected( $login_url, $wp_page_url ); ?> >
					<?php echo get_the_title( $wp_page_id ); ?>
												</option>
					<?php
				}
			}
			?>
									</select>
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
						<div class="group" id="<?php echo esc_attr( 'logout-' . $key ); ?>">
							<h3><?php esc_html_e( $roles[ $key ]['name'], 'bp-redirect' ); ?></h3>
							<div>
			<?php
			$logout_component = '';
			$logout_url       = '';
			$logout_type_val  = '';
			if ( ! empty( $saved_setting ) ) {
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
															<?php
															if ( isset( $logout_type_val ) && 'custom' === $logout_type_val ) {
																echo "checked = 'checked'";
															}
															?>
									>
									<label for="<?php echo esc_attr( 'bp_logout_redirect_settings_' . $key . '_logout_type_custom' ); ?>"><?php esc_html_e( 'Page', 'bp-redirect' ); ?></label>
								</div>
								<div class="bpr-col-6">
									<input name='<?php echo esc_attr( "bp_logout_redirect_settings[$key][logout_type]" ); ?>' id='<?php echo esc_attr( 'bp_logout_redirect_settings_' . $key . '_logout_type_none' ); ?>' value="none" type="radio" class="bp_redi_logout_type"
															<?php
															if ( isset( $logout_type_val ) && 'none' === $logout_type_val ) {
																echo "checked = 'checked'";
															}
															?>
									>
									<label for="<?php echo esc_attr( 'bp_logout_redirect_settings_' . $key . '_logout_type_none' ); ?>"><?php esc_html_e( 'None', 'bp-redirect' ); ?></label>
								</div>

								<div class="bpr-col-6">
			<?php $wp_page_ids = $this->bp_redirect_get_all_page_ids(); ?>
									<select name='<?php echo esc_attr( "bp_logout_redirect_settings[$key][logout_url]" ); ?>' class="bpr-logout-custom
															<?php
															if ( isset( $logout_type_val ) && 'custom' === $logout_type_val ) {
																echo 'bpr_show';
															}
															?>
									" >
			<?php
			if ( $wp_page_ids ) {
				foreach ( $wp_page_ids as $wp_page_id ) {
					$wp_page_url = get_permalink( $wp_page_id );
					?>
												<option value="<?php echo esc_attr( $wp_page_url ); ?>" <?php selected( $logout_url, $wp_page_url ); ?> >
					<?php echo esc_html_e( get_the_title( $wp_page_id ), 'bp-redirect' ); ?>
												</option>
					<?php
				}
			}
			?>
									</select>
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
	 *  Actions performed for saving admin settings.
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function bp_redirect_save_admin_settings() {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bp-js-admin-ajax-nonce' ) ) {
			if ( isset( $_POST['action'] ) && 'bp_redirect_admin_settings' === $_POST['action'] ) {
				$saved_setting = bp_get_option( 'bp_redirect_admin_settings' );
				parse_str( $_POST['login_details'], $login_form_data );
				parse_str( $_POST['logout_details'], $logout_form_data );
				// parse_str( $_POST['enable_disable_setting'], $enable_disable_setting );
				// parse_str( $_POST['enable_disable_role_setting'], $enable_disable_role_setting );
				$login_details  = filter_var_array( $login_form_data, FILTER_SANITIZE_STRING );
				$logout_details = filter_var_array( $logout_form_data, FILTER_SANITIZE_STRING );
				// $member_type_setting = filter_var_array( $enable_disable_setting, FILTER_SANITIZE_STRING );
				// $role_setting        = filter_var_array( $enable_disable_role_setting, FILTER_SANITIZE_STRING );
				$login_array_keys  = array();
				$logout_array_keys = array();
				if ( ! empty( $saved_setting ) && $saved_setting['bp_login_redirect_settings'] && isset( $saved_setting['bp_logout_redirect_settings'] ) ) {
					$login_array_keys  = array_keys( $saved_setting['bp_login_redirect_settings'] );
					$logout_array_keys = array_keys( $saved_setting['bp_logout_redirect_settings'] );
				} else {
					$saved_setting = array();
				}
				foreach ( $login_details['bp_login_redirect_settings'] as $key => $lgn_detail ) {
					if ( in_array( $key, $login_array_keys, true ) ) {
						unset( $saved_setting['bp_login_redirect_settings'][ $key ] );
						$saved_setting['bp_login_redirect_settings'][ $key ] = $lgn_detail;
					} else {
						$saved_setting['bp_login_redirect_settings'][ $key ] = $lgn_detail;
					}
				}
				foreach ( $logout_details['bp_logout_redirect_settings'] as $key => $lgt_detail ) {
					if ( in_array( $key, $logout_array_keys, true ) ) {
						unset( $saved_setting['bp_logout_redirect_settings'][ $key ] );
						$saved_setting['bp_logout_redirect_settings'][ $key ] = $lgt_detail;
					} else {
						$saved_setting['bp_logout_redirect_settings'][ $key ] = $lgt_detail;
					}
				}
				if ( isset( $_POST['enable_disable_setting'] ) && '' !== $_POST['enable_disable_setting'] ) {
					$saved_setting['member_type_btn_value'] = sanitize_text_field( $_POST['enable_disable_setting'] );
				}
				if ( isset( $_POST['enable_disable_role_setting'] ) && '' !== $_POST['enable_disable_role_setting'] ) {
					$saved_setting['role_btn_value'] = sanitize_text_field( $_POST['enable_disable_role_setting'] );
				}
				$saved_setting['loginSequence']  = sanitize_text_field( $_POST['loginSequence'] );
				$saved_setting['logoutSequence'] = sanitize_text_field( $_POST['logoutSequence'] );
				bp_update_option( 'bp_redirect_admin_settings', $saved_setting );
			}
		}
		exit;
	}


}
