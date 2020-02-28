<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/contact/
 * @since      1.0.0
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
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $bp_redirect    The ID of this plugin.
	 */
	private $bp_redirect;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Holds options key
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */

	protected $options_key = 'bp_redirect_setting';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $bp_redirect       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $bp_redirect, $version ) {

		$this->bp_redirect = $bp_redirect;
		$this->version     = $version;
	}

	/**
	 * Builds plugin admin menu and pages
	 *
	 * @since 1.0.0
	 * @author  Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */

	public function admin_menu() {

		add_menu_page( __( 'BP Redirect Setting Page', BP_REDIRECT_DOMAIN ), __( 'BP Redirect', BP_REDIRECT_DOMAIN ), 'manage_options', 'bp_redirect_settings', array( $this, 'bp_redirect_settings_page' ), 'dashicons-external' );

		add_submenu_page( 'bp_redirect_settings', __( 'General', BP_REDIRECT_DOMAIN ), __( ' General ', BP_REDIRECT_DOMAIN ), 'manage_options', 'bp_redirect_settings' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */

	public function enqueue_styles() {

		wp_enqueue_style( $this->bp_redirect, plugin_dir_url( __FILE__ ) . 'assets/css/bp-redirect-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */

	public function enqueue_scripts() {

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-accordion' );

		wp_enqueue_script( $this->bp_redirect, plugin_dir_url( __FILE__ ) . 'assets/js/bp-redirect-admin.js', array( 'jquery' ), $this->version, false );
		if ( ! wp_script_is( 'jquery-ui-sortable', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
		}

	}


	/**
	 * Builds plugin admin setting page
	 *
	 * @since 1.0.0
	 * @author  Wbcom Designs <admin@wbcomdesigns.com>
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
						function( $key1, $key2 ) use ( $seq ) {
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
						function( $logoutkey1, $logoutkey2 ) use ( $logoutseq ) {
							return ( array_search( $logoutkey1, $logoutseq ) > array_search( $logoutkey2, $logoutseq ) );
						}
					);
				}
			}
		}

		?>
		<h1><?php _e( 'BP Redirect Settings', BP_REDIRECT_DOMAIN ); ?></h1>
		<div id="bpredirect-settings_updated" class="updated settings-error notice is-dismissible">
			<p><strong><?php _e( 'Settings saved.', BP_REDIRECT_DOMAIN ); ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', BP_REDIRECT_DOMAIN ); ?></span></button>
		</div>
		<div class="bpr-row">
			<div class="bpr-col-8">
				<!-- login Settings -->
				<h2><?php _e( 'Login Redirect Settings', BP_REDIRECT_DOMAIN ); ?></h2>
				<?php $this->bp_redirect_plugin_login_settings( $loginSequence, $bp_pages_ids, $saved_setting ); ?>
				<!-- Logout Settings -->
				<h2><?php _e( 'Logout Redirect Settings', BP_REDIRECT_DOMAIN ); ?></h2>
				<?php $this->bp_redirect_plugin_logout_settings( $logoutSequence, $bp_pages_ids, $saved_setting ); ?>
			</div>
			<div class="bpr-col-4" id="bpr-faq-section">
				<!-- FAQ(s) -->
				<h2><?php _e( 'FAQ(s)', BP_REDIRECT_DOMAIN ); ?></h2>
				<?php $this->bp_redirect_faqs(); ?>
			</div>
		</div>
		<p>
			<button id="bp-redirect-settings-submit" class="button-primary" name="bp-redirect-settings-submit"><?php _e( 'Save Settings', BP_REDIRECT_DOMAIN ); ?></button><img src="<?php _e( $spinner_src, BP_REDIRECT_DOMAIN ); ?>" class="bp-redirect-settings-spinner" />
		</p>
		<?php
	}

	/**
	 * Get all roles
	 *
	 * This function is used for get roles.
	 *
	 * @since 1.0.0
	 * @author  Wbcom Designs <admin@wbcomdesigns.com>
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
	 * @since 1.0.0
	 * @author  Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */

	public function bp_redirect_plugin_login_settings( $roles, $bp_pages_ids, $saved_setting ) {
		?>
		  <div class="bpr-col-12">
			<form method="post" id="bpr-login-settings-form">
				<div id="bgr-login-accordion">
					<?php foreach ( $roles as $key => $val ) { ?>
						<div class="group" id="<?php echo 'login-' . $key; ?>" >
							 <h3><?php _e( $roles[ $key ]['name'], BP_REDIRECT_DOMAIN ); ?></h3>
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
								 // Makes sure the plugin is defined before trying to use it
								if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
									require_once ABSPATH . '/wp-admin/includes/plugin.php';
								}
								if ( is_plugin_active_for_network( 'buddypress/bp-loader.php' ) === true ) {
									?>
										<div class="bpr-col-4">
											<input name='<?php echo "bp_login_redirect_settings[$key][login_type]"; ?>' id= '<?php echo 'bp_login_redirect_settings_' . $key . '_login_type_referer'; ?>' value="referer" type="radio" class="bp_redi_login_type"
																	<?php
																	if ( isset( $login_type_val ) && $login_type_val == 'referer' ) {
																		echo "checked = 'checked'"; }
																	?>
											>
											<label for="<?php echo 'bp_login_redirect_settings_' . $key . '_login_type_referer'; ?>"><?php _e( 'BuddyPress Component', BP_REDIRECT_DOMAIN ); ?></label>
										</div>
										<?php
								}
							} elseif ( in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', bp_get_option( 'active_plugins' ) ) ) ) {
								?>
								<div class="bpr-col-4">
									<input name='<?php echo "bp_login_redirect_settings[$key][login_type]"; ?>' id= '<?php echo 'bp_login_redirect_settings_' . $key . '_login_type_referer'; ?>' value="referer" type="radio" class="bp_redi_login_type"
															<?php
															if ( isset( $login_type_val ) && $login_type_val == 'referer' ) {
																echo "checked = 'checked'"; }
															?>
									>
									<label for="<?php echo 'bp_login_redirect_settings_' . $key . '_login_type_referer'; ?>"><?php _e( 'BuddyPress Component', BP_REDIRECT_DOMAIN ); ?></label>
								</div>
								<?php } ?>
								<div class="bpr-col-4">
									<input name='<?php echo "bp_login_redirect_settings[$key][login_type]"; ?>' id='<?php echo 'bp_login_redirect_settings_' . $key . '_login_type_custom'; ?>' value="custom" type="radio" class="bp_redi_login_type"
															<?php
															if ( isset( $login_type_val ) && $login_type_val == 'custom' ) {
																echo "checked = 'checked'"; }
															?>
									>
									<label for="<?php echo 'bp_login_redirect_settings_' . $key . '_login_type_custom'; ?>"><?php _e( 'Page', BP_REDIRECT_DOMAIN ); ?></label>
								</div>
								<div class="bpr-col-4">
									<input name='<?php echo "bp_login_redirect_settings[$key][login_type]"; ?>' id='<?php echo 'bp_login_redirect_settings_' . $key . '_login_type_none'; ?>' value="none" type="radio" class="bp_redi_login_type"
															<?php
															if ( isset( $login_type_val ) && $login_type_val == 'none' ) {
																echo "checked = 'checked'"; }
															?>
									>
									<label for="<?php echo 'bp_login_redirect_settings_' . $key . '_login_type_none'; ?>"><?php _e( 'None', BP_REDIRECT_DOMAIN ); ?></label>
								</div>
								<div class="bpr-col-4">
									<select name='<?php echo "bp_login_redirect_settings[$key][login_component]"; ?>' class='bpr-login-component
															 <?php
																if ( isset( $login_type_val ) && $login_type_val == 'referer' ) {
																	echo 'bpr_show'; }
																?>
									 ' >
										<option value=''><?php _e( 'Select', BP_REDIRECT_DOMAIN ); ?></option>
									<?php if ( bp_is_active( 'members' ) ) { ?>
												<option value= "<?php echo 'profile'; ?>"
																		   <?php
																			if ( $login_component == 'profile' ) {
																				echo "selected = 'selected'"; }
																			?>
												 ><?php _e( 'Member Profile', BP_REDIRECT_DOMAIN ); ?>
												</option>
									<?php	} if ( bp_is_active( 'activity' ) ) { ?>
												<option value= "<?php echo 'member_activity'; ?>"
																		   <?php
																			if ( $login_component == 'member_activity' ) {
																				echo "selected = 'selected'"; }
																			?>
												 ><?php _e( 'Member Activity', BP_REDIRECT_DOMAIN ); ?>
												</option>
										<?php
									}
										$bp_pages = bp_core_get_directory_page_ids();
										$pages    = get_pages( array( 'include' => $bp_pages ) );
									foreach ( $pages as $page ) {
										if ( $page->post_title == 'Activity' ) {
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
									<select name='<?php echo "bp_login_redirect_settings[$key][login_url]"; ?>' class='bpr-login-custom
															 <?php
																if ( isset( $login_type_val ) && $login_type_val == 'custom' ) {
																	echo 'bpr_show'; }
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
	 * @since 1.0.0
	 * @author Wbcom Designs <admin@wbcomdesigns.com>
	 * @access public
	 */

	public function bp_redirect_plugin_logout_settings( $roles, $bp_pages_ids, $saved_setting ) {
		?>
		  <div class="bpr-col-12">
			<form method="post" id="bpr-logout-settings-form">
				<div id="bgr-logout-accordion">
					<?php foreach ( $roles as $key => $val ) { ?>
						<div class="group" id="<?php echo 'logout-' . $key; ?>">
							 <h3><?php _e( $roles[ $key ]['name'], BP_REDIRECT_DOMAIN ); ?></h3>
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
									<input name='<?php echo "bp_logout_redirect_settings[$key][logout_type]"; ?>' id='<?php echo 'bp_logout_redirect_settings_' . $key . '_logout_type_custom'; ?>' value="custom" type="radio" class="bp_redi_logout_type"
															<?php
															if ( isset( $logout_type_val ) && $logout_type_val == 'custom' ) {
																echo "checked = 'checked'"; }
															?>
									>
									<label for="<?php echo 'bp_logout_redirect_settings_' . $key . '_logout_type_custom'; ?>"><?php _e( 'Page', BP_REDIRECT_DOMAIN ); ?></label>
								</div>
								<div class="bpr-col-6">
									<input name='<?php echo "bp_logout_redirect_settings[$key][logout_type]"; ?>' id='<?php echo 'bp_logout_redirect_settings_' . $key . '_logout_type_none'; ?>' value="none" type="radio" class="bp_redi_logout_type"
															<?php
															if ( isset( $logout_type_val ) && $logout_type_val == 'none' ) {
																echo "checked = 'checked'"; }
															?>
									>
									<label for="<?php echo 'bp_logout_redirect_settings_' . $key . '_logout_type_none'; ?>"><?php _e( 'None', BP_REDIRECT_DOMAIN ); ?></label>
								</div>

								<div class="bpr-col-6">
									<?php $wp_page_ids = $this->bp_redirect_get_all_page_ids(); ?>
									<select name='<?php echo "bp_logout_redirect_settings[$key][logout_url]"; ?>' class="bpr-logout-custom
															 <?php
																if ( isset( $logout_type_val ) && $logout_type_val == 'custom' ) {
																	echo 'bpr_show'; }
																?>
									" >
										<?php
										if ( $wp_page_ids ) {
											foreach ( $wp_page_ids as $wp_page_id ) {
												$wp_page_url = get_permalink( $wp_page_id );
												?>
												<option value="<?php echo $wp_page_url; ?>" <?php selected( $logout_url, $wp_page_url ); ?> >
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
				 * Registers BP Redirect settings
				 *
				 * This is used because register_setting() isn't available until the "admin_init" hook.
				 *
				 * @since 1.0.0
				 * @author Wbcom Designs <admin@wbcomdesigns.com>
				 * @access public
				 */
	public function admin_init() {

		// Register setting
		register_setting( 'bp_redirect_settings', 'bp_redirect_settings', array( $this, 'save_settings' ) );

		// Add sections
		add_settings_section( 'general', __( 'General', BP_REDIRECT_DOMAIN ), '__return_false', $this->options_key );
	}

	/**
	 *  Actions performed for saving admin settings
	 *
	 *  @since   1.0.0
	 *  @author  Wbcom Designs
	 *  @access public
	 */
	public function bp_redirect_save_admin_settings() {
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'bp_redirect_admin_settings' ) {
			parse_str( $_POST['login_details'], $login_form_data );
			parse_str( $_POST['logout_details'], $logout_form_data );
			$login_details  = filter_var_array( $login_form_data, FILTER_SANITIZE_STRING );
			$logout_details = filter_var_array( $logout_form_data, FILTER_SANITIZE_STRING );
			$setting_arr    = array_merge( $login_details, $logout_details );
			if ( ! empty( $setting_arr ) && ! empty( $_POST['loginSequence'] ) ) {
				$setting_arr['loginSequence']  = sanitize_text_field( $_POST['loginSequence'] );
				$setting_arr['logoutSequence'] = sanitize_text_field( $_POST['logoutSequence'] );
				bp_update_option( 'bp_redirect_admin_settings', $setting_arr );
			}
		}
		exit;
	}

	/**
	 *  Display faq(s)
	 *
	 *  @since   1.0.0
	 *  @author  Wbcom Designs
	 *  @access public
	 */
	public function bp_redirect_faqs() {
		?>
		<div class="bpr-col-12">
			<div class="bpr-row">
				<div class="bpr-ques">
		<?php _e( 'Is this plugin requires another plugin?', BP_REDIRECT_DOMAIN ); ?>
				</div>
				<div class="bpr-ans">
					<?php _e( 'Yes, this plugin requires BuddyPress plugin.', BP_REDIRECT_DOMAIN ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
					<?php _e( 'Where it redirects if no option selected or in the case of empty custom URL field?', BP_REDIRECT_DOMAIN ); ?>
				</div>
				<div class="bpr-ans">
					<?php _e( 'In that case, plugin follows default redirection rule.', BP_REDIRECT_DOMAIN ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
					<?php _e( 'Where it redirects, when we select "Member profile" in BuddyPress Component dropdown?', BP_REDIRECT_DOMAIN ); ?>
				</div>
				<div class="bpr-ans">
					<?php _e( "It redirects to logged in member's profile page", BP_REDIRECT_DOMAIN ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
					<?php _e( 'Where it redirects, when we select "Member activity" in BuddyPress Component dropdown?', BP_REDIRECT_DOMAIN ); ?>
				</div>
				<div class="bpr-ans">
					<?php _e( "It redirects to logged in member's activity page", BP_REDIRECT_DOMAIN ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
					<?php _e( 'Where it redirects, when we select "Activity" in BuddyPress Component dropdown?', BP_REDIRECT_DOMAIN ); ?>
				</div>
				<div class="bpr-ans">
					<?php _e( 'It redirects to logged in site wide activity page', BP_REDIRECT_DOMAIN ); ?>
				</div>
			</div>
			<div class="bpr-row">
				<div class="bpr-ques">
					<?php _e( 'Where do I ask for support?', BP_REDIRECT_DOMAIN ); ?>
				</div>
				<div class="bpr-ans">
					<?php _e( "Please visit <a href='http://wbcomdesigns.com/contact' rel='nofollow'>Wbcom Designs</a> for any query related to plugin and BuddyPress.", BP_REDIRECT_DOMAIN ); ?>
				</div>
			</div>
		</div>

		<?php
	}
}
