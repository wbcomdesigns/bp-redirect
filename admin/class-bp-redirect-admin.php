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
	 * Builds plugin admin menu and pages
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */
	public function admin_menu() {

		add_menu_page( __( 'BP Redirect Setting Page', 'bp-redirect' ), __( 'BP Redirect', 'bp-redirect' ), 'manage_options', 'bp_redirect_settings', array( $this, 'bp_redirect_settings_page' ), 'dashicons-external' );

		add_submenu_page( 'bp_redirect_settings', __( 'General', 'bp-redirect' ), __( ' General ', 'bp-redirect' ), 'manage_options', 'bp_redirect_settings' );
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
	 * Builds plugin admin setting page
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */
	public function bp_redirect_settings_page() {
		$spinner_src   = includes_url() . 'images/spinner.gif';
		$saved_setting = bp_get_option( 'bp_redirect_admin_settings' );

		$bp_pages      = bp_get_option( 'bp-pages' );
		$bp_pages_ids  = array_values( $bp_pages );
		$loginSequence = $this->get_editable_roles();
		if ( ! empty( $saved_setting ) ) {
			if ( array_key_exists( 'loginSequence', $saved_setting ) ) {
				$seq = explode( ',', $saved_setting['loginSequence'] );
				foreach ( $seq as $key => $val ) {
					$val_arr     = explode( '-', $val );
					$seq[ $key ] = $val_arr[1];
				}
				if ( ! empty( $seq ) ) {
					uksort(
						$loginSequence,
						function ( $key1, $key2 ) use ( $seq ) {
							return ( array_search( $key1, $seq ) > array_search( $key2, $seq ) );
						}
					);
				}
			}
		}

		$logoutSequence = $this->get_editable_roles();
		if ( ! empty( $saved_setting ) ) {
			if ( array_key_exists( 'logoutSequence', $saved_setting ) ) {
				$logoutseq = explode( ',', $saved_setting['logoutSequence'] );
				foreach ( $logoutseq as $key => $val ) {
					$val_arr           = explode( '-', $val );
					$logoutseq[ $key ] = $val_arr[1];
				}
				if ( ! empty( $logoutseq ) ) {
					uksort(
						$logoutSequence,
						function ( $logoutkey1, $logoutkey2 ) use ( $logoutseq ) {
							return ( array_search( $logoutkey1, $logoutseq ) > array_search( $logoutkey2, $logoutseq ) );
						}
					);
				}
			}
		}

		?>
		<h1><?php esc_html_e( 'BP Redirect Settings', 'bp-redirect' ); ?></h1>
		<div id="bpredirect-settings_updated" class="updated settings-error notice is-dismissible">
			<p><strong><?php esc_html_e( 'Settings saved.', 'bp-redirect' ); ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'bp-redirect' ); ?></span></button>
		</div>
		<div class="bpr-row">
			<div class="bpr-col-8">
				<!-- login Settings -->
				<h2><?php esc_html_e( 'Login Redirect Settings', 'bp-redirect' ); ?></h2>
		<?php $this->bp_redirect_plugin_login_settings( $loginSequence, $bp_pages_ids, $saved_setting ); ?>
				<!-- Logout Settings -->
				<h2><?php esc_html_e( 'Logout Redirect Settings', 'bp-redirect' ); ?></h2>
		<?php $this->bp_redirect_plugin_logout_settings( $logoutSequence, $bp_pages_ids, $saved_setting ); ?>
			</div>
			<div class="bpr-col-4" id="bpr-faq-section">
				<!-- FAQ(s) -->
				<h2><?php esc_html_e( 'FAQ(s)', 'bp-redirect' ); ?></h2>
		<?php $this->bp_redirect_faqs(); ?>
			</div>
		</div>
		<p>
			<button id="bp-redirect-settings-submit" class="button-primary" name="bp-redirect-settings-submit"><?php esc_html_e( 'Save Settings', 'bp-redirect' ); ?></button><img src="<?php esc_attr__( $spinner_src, 'bp-redirect' ); ?>" class="bp-redirect-settings-spinner" />
		</p>
		<?php
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
												<option value= "<?php echo 'profile'; ?>"
																		<?php
																		if ( 'profile' === $login_component ) {
																			echo "selected = 'selected'";
																		}
																		?>
												><?php esc_html_e( 'Member Profile', 'bp-redirect' ); ?>
												</option>
			<?php	} if ( bp_is_active( 'activity' ) ) { ?>
												<option value= "<?php echo 'member_activity'; ?>"
																		<?php
																		if ( 'member_activity' === $login_component ) {
																			echo "selected = 'selected'";
																		}
																		?>
												><?php esc_html_e( 'Member Activity', 'bp-redirect' ); ?>
												</option>
				<?php
			}
										$bp_pages = bp_core_get_directory_page_ids();
										$pages    = get_pages( array( 'include' => $bp_pages ) );
			foreach ( $pages as $page ) {
				if ( 'Activity' === $page->post_title ) {
					$option  = '<option value="' . get_page_link( $page->ID ) . '">';
					$option .= $page->post_title;
					$option .= '</option>';
					echo esc_html_e( $option );
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
												<option value="<?php echo esc_attr( $wp_page_url ); ?>" <?php selected( $login_url, $wp_page_url ); ?> >
					<?php echo esc_html_e( get_the_title( $wp_page_id ) ); ?>
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
					<?php echo esc_html_e( get_the_title( $wp_page_id ) ); ?>
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
	 * Registers BP Redirect settings
	 *
	 * This is used because register_setting() isn't available until the "admin_init" hook.
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */
	public function admin_init() {

		// Register setting.
		register_setting( 'bp_redirect_settings', 'bp_redirect_settings', array( $this, 'save_settings' ) );

		// Add sections.
		add_settings_section( 'general', __( 'General', 'bp-redirect' ), '__return_false', $this->options_key );
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
				parse_str( sanitize_text_field( wp_unslash( $_POST['login_details'] ) ), $login_form_data );
				parse_str( sanitize_text_field( wp_unslash( $_POST['logout_details'] ) ), $logout_form_data );
				$login_details  = filter_var_array( $login_form_data, FILTER_SANITIZE_STRING );
				$logout_details = filter_var_array( $logout_form_data, FILTER_SANITIZE_STRING );
				$setting_arr    = array_merge( $login_details, $logout_details );
				if ( ! empty( $setting_arr ) && ! empty( $_POST['loginSequence'] ) ) {
					$setting_arr['loginSequence']  = sanitize_text_field( wp_unslash( $_POST['loginSequence'] ) );
					$setting_arr['logoutSequence'] = sanitize_text_field( wp_unslash( $_POST['logoutSequence'] ) );
					bp_update_option( 'bp_redirect_admin_settings', $setting_arr );
				}
			}
		}
		exit;
	}

	/**
	 *  Display faq(s)
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function bp_redirect_faqs() {
		?>
		<div class="bpr-col-12">
			<div class="bpr-row">
				<div class="bpr-ques">
		<?php esc_html_e( 'Is this plugin requires another plugin?', 'bp-redirect' ); ?>
				</div>
				<div class="bpr-ans">
		<?php esc_html_e( 'Yes, this plugin requires BuddyPress plugin.', 'bp-redirect' ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
		<?php esc_html_e( 'Where it redirects if no option selected or in the case of empty custom URL field?', 'bp-redirect' ); ?>
				</div>
				<div class="bpr-ans">
		<?php esc_html_e( 'In that case, plugin follows default redirection rule.', 'bp-redirect' ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
		<?php esc_html_e( 'Where it redirects, when we select "Member profile" in BuddyPress Component dropdown?', 'bp-redirect' ); ?>
				</div>
				<div class="bpr-ans">
		<?php esc_html_e( "It redirects to logged in member's profile page", 'bp-redirect' ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
		<?php esc_html_e( 'Where it redirects, when we select "Member activity" in BuddyPress Component dropdown?', 'bp-redirect' ); ?>
				</div>
				<div class="bpr-ans">
		<?php esc_html_e( "It redirects to logged in member's activity page", 'bp-redirect' ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
		<?php esc_html_e( 'Where it redirects, when we select "Activity" in BuddyPress Component dropdown?', 'bp-redirect' ); ?>
				</div>
				<div class="bpr-ans">
		<?php esc_html_e( 'It redirects to logged in site wide activity page', 'bp-redirect' ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
		<?php esc_html_e( 'Where do I ask for support?', 'bp-redirect' ); ?>
				</div>
				<div class="bpr-ans">
		<?php _e( "Please visit <a href='http://wbcomdesigns.com/contact' rel='nofollow'>Wbcom Designs</a> for any query related to plugin and BuddyPress.", 'bp-redirect' ); ?>
				</div>
			</div>
		</div>

		<?php
	}
}
