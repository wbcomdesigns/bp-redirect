<?php
/**
 * Admin settings page, AJAX handlers, and shared form renderer.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_Admin
 *
 * Manages the plugin settings page under WB Plugins, renders tabs
 * (Welcome, Global, Roles, integration types, FAQ), and handles AJAX save.
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_Admin {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Admin page slug (matches the existing menu item).
	 *
	 * @var string
	 */
	private $plugin_slug = 'bp-redirect';

	/**
	 * Registered admin tabs keyed by slug.
	 *
	 * @var array
	 */
	private $tabs = array();

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name Plugin slug.
	 * @param string $version     Plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register admin menu.
	 */
	public function add_menu() {
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page(
				__( 'WB Plugins', 'bp-redirect' ),
				__( 'WB Plugins', 'bp-redirect' ),
				'manage_options',
				'wbcomplugins',
				array( $this, 'render_settings_page' ),
				'dashicons-lightbulb',
				59
			);
			add_submenu_page( 'wbcomplugins', __( 'General', 'bp-redirect' ), __( 'General', 'bp-redirect' ), 'manage_options', 'wbcomplugins' );
		}

		add_submenu_page(
			'wbcomplugins',
			__( 'Login Redirect', 'bp-redirect' ),
			__( 'Login Redirect', 'bp-redirect' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Initialize settings sections and build tab list.
	 */
	public function init_settings() {
		// Core tabs.
		$this->tabs['welcome'] = __( 'Welcome', 'bp-redirect' );

		add_settings_section( 'wbcom-redirect-welcome', ' ', array( $this, 'render_welcome_tab' ), 'wbcom-redirect-welcome' );

		$this->tabs['global'] = __( 'Global Redirection', 'bp-redirect' );
		add_settings_section( 'wbcom-redirect-global', ' ', array( $this, 'render_global_tab' ), 'wbcom-redirect-global' );

		$this->tabs['roles'] = __( 'User Roles', 'bp-redirect' );
		add_settings_section( 'wbcom-redirect-roles', ' ', array( $this, 'render_roles_tab' ), 'wbcom-redirect-roles' );

		// Dynamic tabs from integrations with group types (e.g. BP member types).
		if ( class_exists( 'Wbcom_Redirect_Integration_Registry' ) ) {
			foreach ( Wbcom_Redirect_Integration_Registry::instance()->get_with_admin_tabs() as $integration ) {
				$tab_key = 'integration-' . $integration->get_slug();
				/* translators: %s is the integration name */
				$this->tabs[ $tab_key ] = sprintf( __( '%s Types', 'bp-redirect' ), $integration->get_name() );
				add_settings_section(
					'wbcom-redirect-' . $tab_key,
					' ',
					function () use ( $integration ) {
						$this->render_integration_tab( $integration );
					},
					'wbcom-redirect-' . $tab_key
				);
			}
		}

		$this->tabs['faq'] = __( 'FAQ', 'bp-redirect' );
		add_settings_section( 'wbcom-redirect-faq', ' ', array( $this, 'render_faq_tab' ), 'wbcom-redirect-faq' );
	}

	/**
	 * Render the main settings page.
	 */
	public function render_settings_page() {
		$current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'welcome'; // phpcs:ignore
		?>
		<div class="wrap">
			<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo"></div>
			</div>
			<div class="wbcom-wrap bp-member-blog-wrap">
				<div class="blpro-header">
					<div class="wbcom_admin_header-wrapper">
						<div id="wb_admin_plugin_name">
							<?php esc_html_e( 'Login Logout Redirect', 'bp-redirect' ); ?>
							<span>
							<?php
							/* translators: %s is the plugin version number */
							printf( esc_html__( 'Version %s', 'bp-redirect' ), esc_html( WBCOM_REDIRECT_VERSION ) );
							?>
						</span>
						</div>
						<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
					</div>
				</div>
				<?php settings_errors(); ?>
				<div class="wbcom-admin-settings-page">
					<?php $this->render_tabs( $current_tab ); ?>
					<?php do_settings_sections( 'wbcom-redirect-' . $current_tab ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render tab navigation.
	 *
	 * @param string $current_tab Active tab key.
	 */
	private function render_tabs( $current_tab ) {
		echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html__( 'Menu', 'bp-redirect' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';

		foreach ( $this->tabs as $key => $label ) {
			$active = ( $current_tab === $key ) ? 'nav-tab-active' : '';
			$url    = admin_url( 'admin.php?page=' . $this->plugin_slug . '&tab=' . $key );
			printf(
				'<li class="wbcom-redirect-%1$s"><a class="nav-tab %2$s" href="%3$s">%4$s</a></li>',
				esc_attr( $key ),
				esc_attr( $active ),
				esc_url( $url ),
				esc_html( $label )
			);
		}

		echo '</ul></div></div>';
	}

	/**
	 * Welcome tab content.
	 */
	public function render_welcome_tab() {
		include __DIR__ . '/partials/welcome.php';
	}

	/**
	 * FAQ tab content.
	 */
	public function render_faq_tab() {
		include __DIR__ . '/partials/faq.php';
	}

	/**
	 * Global redirection tab.
	 */
	public function render_global_tab() {
		$settings    = get_option( 'wbcom_redirect_global', array() );
		$enabled     = isset( $settings['enabled'] ) ? $settings['enabled'] : 'no';
		$login_cfg   = isset( $settings['login'] ) ? $settings['login'] : array();
		$logout_cfg  = isset( $settings['logout'] ) ? $settings['logout'] : array();
		$spinner_src = includes_url( 'images/spinner.gif' );
		?>
		<div class="wbcom-tab-content">
			<div class="wbcom-wrapper-admin">
				<div class="wbcom-admin-title-section">
					<h3><?php esc_html_e( 'Global Redirection Settings', 'bp-redirect' ); ?></h3>
				</div>
				<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
					<div class="wbcom-settings-section-wrap wbcom-redirect-form" data-context="global">

						<div class="enable_disable_btn wbcom-settings-section-wrap">
							<label for="wbcom-redirect-global-enable" class="enable_disable_setting">
								<?php esc_html_e( 'Enable Global Redirection', 'bp-redirect' ); ?>
							</label>
							<input type="checkbox" class="wppd-ui-toggle wbcom-redirect-toggle" id="wbcom-redirect-global-enable" data-target=".wbcom-redirect-global-fields" <?php checked( $enabled, 'yes' ); ?>>
							<input type="hidden" name="enabled" value="<?php echo esc_attr( $enabled ); ?>">
						</div>

						<div class="wbcom-redirect-global-fields bpr-row bpr-row-wrapper wbcom-settings-section-wrap" <?php echo 'yes' !== $enabled ? 'style="display:none"' : ''; ?>>

							<h4><?php esc_html_e( 'Login Redirect', 'bp-redirect' ); ?></h4>
							<?php $this->render_redirect_form( 'login', $login_cfg ); ?>

							<h4><?php esc_html_e( 'Logout Redirect', 'bp-redirect' ); ?></h4>
							<?php $this->render_redirect_form( 'logout', $logout_cfg ); ?>

						</div>

						<p>
							<button type="button" class="button button-primary wbcom-redirect-save" data-scope="global"><?php esc_html_e( 'Save Settings', 'bp-redirect' ); ?></button>
							<img src="<?php echo esc_url( $spinner_src ); ?>" alt="" class="wbcom-redirect-spinner" style="display:none" />
						</p>
						<div class="wbcom-redirect-notice is-dismissible" style="display:none">
							<p><strong><?php esc_html_e( 'Settings saved.', 'bp-redirect' ); ?></strong></p>
							<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'bp-redirect' ); ?></span></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * User Roles tab.
	 */
	public function render_roles_tab() {
		$settings    = get_option( 'wbcom_redirect_roles', array() );
		$enabled     = isset( $settings['enabled'] ) ? $settings['enabled'] : 'yes';
		$roles_cfg   = isset( $settings['roles'] ) ? $settings['roles'] : array();
		$all_roles   = wp_roles()->roles;
		$spinner_src = includes_url( 'images/spinner.gif' );
		?>
		<div class="wbcom-tab-content">
			<div class="wbcom-wrapper-admin">
				<div class="wbcom-admin-title-section">
					<h3><?php esc_html_e( 'Redirect By User Role', 'bp-redirect' ); ?></h3>
				</div>
				<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
					<div class="wbcom-settings-section-wrap wbcom-redirect-form" data-context="roles">

						<div class="enable_disable_btn wbcom-settings-section-wrap">
							<label for="wbcom-redirect-roles-enable" class="enable_disable_setting">
								<?php esc_html_e( 'Enable User Role Redirection', 'bp-redirect' ); ?>
							</label>
							<input type="checkbox" class="wppd-ui-toggle wbcom-redirect-toggle" id="wbcom-redirect-roles-enable" data-target=".wbcom-redirect-roles-fields" <?php checked( $enabled, 'yes' ); ?>>
							<input type="hidden" name="enabled" value="<?php echo esc_attr( $enabled ); ?>">
						</div>

						<div class="wbcom-redirect-roles-fields bpr-row bpr-row-wrapper wbcom-settings-section-wrap" <?php echo 'yes' !== $enabled ? 'style="display:none"' : ''; ?>>
							<div id="wbcom-redirect-roles-accordion">
								<?php foreach ( $all_roles as $role_slug => $role_data ) : ?>
									<?php
									$role_login  = isset( $roles_cfg[ $role_slug ]['login'] ) ? $roles_cfg[ $role_slug ]['login'] : array();
									$role_logout = isset( $roles_cfg[ $role_slug ]['logout'] ) ? $roles_cfg[ $role_slug ]['logout'] : array();
									?>
									<div class="group" data-role="<?php echo esc_attr( $role_slug ); ?>">
										<h3><?php echo esc_html( translate_user_role( $role_data['name'] ) ); ?></h3>
										<div>
											<h4><?php esc_html_e( 'Login Redirect', 'bp-redirect' ); ?></h4>
											<?php $this->render_redirect_form( 'login', $role_login, 'roles[' . $role_slug . ']' ); ?>

											<h4><?php esc_html_e( 'Logout Redirect', 'bp-redirect' ); ?></h4>
											<?php $this->render_redirect_form( 'logout', $role_logout, 'roles[' . $role_slug . ']' ); ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>

						<p>
							<button type="button" class="button button-primary wbcom-redirect-save" data-scope="roles"><?php esc_html_e( 'Save Settings', 'bp-redirect' ); ?></button>
							<img src="<?php echo esc_url( $spinner_src ); ?>" alt="" class="wbcom-redirect-spinner" style="display:none" />
						</p>
						<div class="wbcom-redirect-notice is-dismissible" style="display:none">
							<p><strong><?php esc_html_e( 'Settings saved.', 'bp-redirect' ); ?></strong></p>
							<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'bp-redirect' ); ?></span></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render an integration group types tab (e.g. BuddyPress Member Types).
	 *
	 * @param Wbcom_Redirect_Integration $integration Integration instance.
	 */
	public function render_integration_tab( $integration ) {
		$option_key  = 'wbcom_redirect_' . $integration->get_slug();
		$settings    = get_option( $option_key, array() );
		$enabled     = isset( $settings['enabled'] ) ? $settings['enabled'] : 'no';
		$groups_cfg  = isset( $settings['groups'] ) ? $settings['groups'] : array();
		$all_types   = $integration->get_all_group_types();
		$spinner_src = includes_url( 'images/spinner.gif' );
		?>
		<div class="wbcom-tab-content">
			<div class="wbcom-wrapper-admin">
				<div class="wbcom-admin-title-section">
					<h3>
						<?php
						/* translators: %s is the integration name */
						printf( esc_html__( 'Redirect By %s Type', 'bp-redirect' ), esc_html( $integration->get_name() ) );
						?>
					</h3>
				</div>
				<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
					<div class="wbcom-settings-section-wrap wbcom-redirect-form" data-context="integration" data-integration="<?php echo esc_attr( $integration->get_slug() ); ?>">

						<div class="enable_disable_btn wbcom-settings-section-wrap">
							<label for="wbcom-redirect-<?php echo esc_attr( $integration->get_slug() ); ?>-enable" class="enable_disable_setting">
								<?php
								/* translators: %s is the integration name */
								printf( esc_html__( 'Enable %s Type Redirection', 'bp-redirect' ), esc_html( $integration->get_name() ) );
								?>
							</label>
							<input type="checkbox" class="wppd-ui-toggle wbcom-redirect-toggle" id="wbcom-redirect-<?php echo esc_attr( $integration->get_slug() ); ?>-enable" data-target=".wbcom-redirect-<?php echo esc_attr( $integration->get_slug() ); ?>-fields" <?php checked( $enabled, 'yes' ); ?>>
							<input type="hidden" name="enabled" value="<?php echo esc_attr( $enabled ); ?>">
						</div>

						<?php if ( empty( $all_types ) ) : ?>
							<p class="description"><?php esc_html_e( 'No types found. Create types in the integration plugin first.', 'bp-redirect' ); ?></p>
						<?php else : ?>
							<div class="wbcom-redirect-<?php echo esc_attr( $integration->get_slug() ); ?>-fields bpr-row bpr-row-wrapper wbcom-settings-section-wrap" <?php echo 'yes' !== $enabled ? 'style="display:none"' : ''; ?>>
								<div id="wbcom-redirect-<?php echo esc_attr( $integration->get_slug() ); ?>-accordion">
									<?php foreach ( $all_types as $type_slug => $type_label ) : ?>
										<?php
										$type_login  = isset( $groups_cfg[ $type_slug ]['login'] ) ? $groups_cfg[ $type_slug ]['login'] : array();
										$type_logout = isset( $groups_cfg[ $type_slug ]['logout'] ) ? $groups_cfg[ $type_slug ]['logout'] : array();
										?>
										<div class="group" data-group="<?php echo esc_attr( $type_slug ); ?>">
											<h3><?php echo esc_html( $type_label ); ?></h3>
											<div>
												<h4><?php esc_html_e( 'Login Redirect', 'bp-redirect' ); ?></h4>
												<?php $this->render_redirect_form( 'login', $type_login, 'groups[' . $type_slug . ']' ); ?>

												<h4><?php esc_html_e( 'Logout Redirect', 'bp-redirect' ); ?></h4>
												<?php $this->render_redirect_form( 'logout', $type_logout, 'groups[' . $type_slug . ']' ); ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<p>
							<button type="button" class="button button-primary wbcom-redirect-save" data-scope="integration" data-integration="<?php echo esc_attr( $integration->get_slug() ); ?>"><?php esc_html_e( 'Save Settings', 'bp-redirect' ); ?></button>
							<img src="<?php echo esc_url( $spinner_src ); ?>" alt="" class="wbcom-redirect-spinner" style="display:none" />
						</p>
						<div class="wbcom-redirect-notice is-dismissible" style="display:none">
							<p><strong><?php esc_html_e( 'Settings saved.', 'bp-redirect' ); ?></strong></p>
							<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'bp-redirect' ); ?></span></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Shared redirect settings form renderer.
	 * Used for every redirect config (global login, global logout, per-role, per-group-type).
	 *
	 * @param string $context 'login' or 'logout'.
	 * @param array  $config  Saved config: type, page_id, custom_url, integration.
	 * @param string $prefix  Field name prefix for nested data (e.g. 'roles[administrator]').
	 */
	public function render_redirect_form( $context, $config = array(), $prefix = '' ) {
		$type        = ! empty( $config['type'] ) ? $config['type'] : 'none';
		$page_id     = ! empty( $config['page_id'] ) ? absint( $config['page_id'] ) : 0;
		$custom_url  = ! empty( $config['custom_url'] ) ? $config['custom_url'] : '';
		$integration = ! empty( $config['integration'] ) ? $config['integration'] : '';

		$name_base = $prefix ? $prefix . '[' . $context . ']' : $context;

		// Get integration destinations.
		$destinations = array();
		if ( class_exists( 'Wbcom_Redirect_Integration_Registry' ) ) {
			$destinations = Wbcom_Redirect_Integration_Registry::instance()->get_all_destinations();
		}

		// Get all pages.
		$pages = get_pages(
			array(
				'post_status' => 'publish',
				'sort_column' => 'post_title',
			)
		);

		$uid = wp_unique_id( 'wbcom-redir-' );
		?>
		<div class="wbcom-redirect-config" data-uid="<?php echo esc_attr( $uid ); ?>">
			<div class="wbcom-redirect-type-options">
				<label>
					<input type="radio" name="<?php echo esc_attr( $name_base . '[type]' ); ?>" value="none" <?php checked( $type, 'none' ); ?> class="wbcom-redirect-type-radio">
					<?php esc_html_e( 'None (WordPress Default)', 'bp-redirect' ); ?>
				</label>

				<label>
					<input type="radio" name="<?php echo esc_attr( $name_base . '[type]' ); ?>" value="page" <?php checked( $type, 'page' ); ?> class="wbcom-redirect-type-radio">
					<?php esc_html_e( 'Page', 'bp-redirect' ); ?>
				</label>

				<label>
					<input type="radio" name="<?php echo esc_attr( $name_base . '[type]' ); ?>" value="custom_url" <?php checked( $type, 'custom_url' ); ?> class="wbcom-redirect-type-radio">
					<?php esc_html_e( 'Custom URL', 'bp-redirect' ); ?>
				</label>

				<?php if ( ! empty( $destinations ) ) : ?>
					<label>
						<input type="radio" name="<?php echo esc_attr( $name_base . '[type]' ); ?>" value="integration" <?php checked( $type, 'integration' ); ?> class="wbcom-redirect-type-radio">
						<?php esc_html_e( 'Integration Destination', 'bp-redirect' ); ?>
					</label>
				<?php endif; ?>
			</div>

			<div class="wbcom-redirect-field wbcom-redirect-field-page" <?php echo 'page' !== $type ? 'style="display:none"' : ''; ?>>
				<select name="<?php echo esc_attr( $name_base . '[page_id]' ); ?>">
					<option value=""><?php esc_html_e( '-- Select Page --', 'bp-redirect' ); ?></option>
					<?php foreach ( $pages as $page ) : ?>
						<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( $page_id, $page->ID ); ?>>
							<?php echo esc_html( $page->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="wbcom-redirect-field wbcom-redirect-field-custom_url" <?php echo 'custom_url' !== $type ? 'style="display:none"' : ''; ?>>
				<input type="url" name="<?php echo esc_attr( $name_base . '[custom_url]' ); ?>" value="<?php echo esc_url( $custom_url ); ?>" placeholder="https://" class="regular-text">
			</div>

			<?php if ( ! empty( $destinations ) ) : ?>
				<div class="wbcom-redirect-field wbcom-redirect-field-integration" <?php echo 'integration' !== $type ? 'style="display:none"' : ''; ?>>
					<select name="<?php echo esc_attr( $name_base . '[integration]' ); ?>">
						<option value=""><?php esc_html_e( '-- Select Destination --', 'bp-redirect' ); ?></option>
						<?php
						$grouped = array();
						foreach ( $destinations as $dest ) {
							$grouped[ $dest->get_integration() ][] = $dest;
						}
						foreach ( $grouped as $int_slug => $dests ) :
							$int_obj  = Wbcom_Redirect_Integration_Registry::instance()->get( $int_slug );
							$int_name = $int_obj ? $int_obj->get_name() : $int_slug;
							?>
							<optgroup label="<?php echo esc_attr( $int_name ); ?>">
								<?php foreach ( $dests as $dest ) : ?>
									<option value="<?php echo esc_attr( $dest->get_slug() ); ?>" <?php selected( $integration, $dest->get_slug() ); ?>>
										<?php echo esc_html( $dest->get_label() ); ?>
									</option>
								<?php endforeach; ?>
							</optgroup>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * AJAX: Save settings for any scope (global, roles, integration).
	 */
	public function ajax_save_settings() {
		check_ajax_referer( 'wbcom-redirect-admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'bp-redirect' ) );
		}

		$scope    = isset( $_POST['scope'] ) ? sanitize_key( $_POST['scope'] ) : '';
		$raw_data = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : ''; // phpcs:ignore

		// Settings arrive as a query string; parse into nested array.
		$data = array();
		if ( is_string( $raw_data ) ) {
			parse_str( $raw_data, $data );
		} elseif ( is_array( $raw_data ) ) {
			$data = $raw_data;
		}

		switch ( $scope ) {
			case 'global':
				$settings = array(
					'enabled' => ! empty( $data['enabled'] ) ? sanitize_key( $data['enabled'] ) : 'no',
					'login'   => $this->sanitize_config( isset( $data['login'] ) ? $data['login'] : array() ),
					'logout'  => $this->sanitize_config( isset( $data['logout'] ) ? $data['logout'] : array() ),
				);
				update_option( 'wbcom_redirect_global', $settings );
				break;

			case 'roles':
				$settings = array(
					'enabled' => ! empty( $data['enabled'] ) ? sanitize_key( $data['enabled'] ) : 'no',
					'roles'   => array(),
				);

				if ( ! empty( $data['roles'] ) && is_array( $data['roles'] ) ) {
					foreach ( $data['roles'] as $role_slug => $role_data ) {
						$role_slug                       = sanitize_key( $role_slug );
						$settings['roles'][ $role_slug ] = array(
							'login'  => $this->sanitize_config( isset( $role_data['login'] ) ? $role_data['login'] : array() ),
							'logout' => $this->sanitize_config( isset( $role_data['logout'] ) ? $role_data['logout'] : array() ),
						);
					}
				}
				update_option( 'wbcom_redirect_roles', $settings );
				break;

			case 'integration':
				$integration_slug = isset( $_POST['integration'] ) ? sanitize_key( $_POST['integration'] ) : '';
				if ( empty( $integration_slug ) ) {
					wp_send_json_error( __( 'Missing integration slug.', 'bp-redirect' ) );
				}

				$settings = array(
					'enabled' => ! empty( $data['enabled'] ) ? sanitize_key( $data['enabled'] ) : 'no',
					'groups'  => array(),
				);

				if ( ! empty( $data['groups'] ) && is_array( $data['groups'] ) ) {
					foreach ( $data['groups'] as $group_slug => $group_data ) {
						$group_slug                        = sanitize_key( $group_slug );
						$settings['groups'][ $group_slug ] = array(
							'login'  => $this->sanitize_config( isset( $group_data['login'] ) ? $group_data['login'] : array() ),
							'logout' => $this->sanitize_config( isset( $group_data['logout'] ) ? $group_data['logout'] : array() ),
						);
					}
				}
				update_option( 'wbcom_redirect_' . $integration_slug, $settings );
				break;

			default:
				wp_send_json_error( __( 'Invalid scope.', 'bp-redirect' ) );
		}

		wp_send_json_success( __( 'Settings saved.', 'bp-redirect' ) );
	}

	/**
	 * Sanitize a single redirect config.
	 *
	 * @param array $config Raw config from POST.
	 * @return array Sanitized config.
	 */
	private function sanitize_config( $config ) {
		$allowed_types = array( 'none', 'page', 'custom_url', 'integration' );
		$type          = isset( $config['type'] ) ? sanitize_key( $config['type'] ) : 'none';

		if ( ! in_array( $type, $allowed_types, true ) ) {
			$type = 'none';
		}

		return array(
			'type'        => $type,
			'page_id'     => isset( $config['page_id'] ) ? absint( $config['page_id'] ) : 0,
			'custom_url'  => isset( $config['custom_url'] ) ? esc_url_raw( $config['custom_url'] ) : '',
			'integration' => isset( $config['integration'] ) ? sanitize_text_field( $config['integration'] ) : '',
		);
	}

	/**
	 * Enqueue admin CSS.
	 */
	public function enqueue_styles() {
		if ( ! $this->is_plugin_page() ) {
			return;
		}

		$admin_css = wbcom_redirect_get_asset_filename( 'admin/assets/css', 'bp-redirect-admin' );
		if ( $admin_css ) {
			wp_enqueue_style( 'wbcom-redirect-admin', WBCOM_REDIRECT_PLUGIN_URL . $admin_css, array(), $this->version );
		}

		if ( ! wp_style_is( 'wbcom-admin-setting-css', 'enqueued' ) ) {
			$wbcom_css = wbcom_redirect_get_asset_filename( 'admin/wbcom/assets/css', 'wbcom-admin-setting' );
			if ( $wbcom_css ) {
				wp_enqueue_style( 'wbcom-admin-setting-css', WBCOM_REDIRECT_PLUGIN_URL . $wbcom_css, array(), $this->version );
			}
		}
	}

	/**
	 * Enqueue admin JS.
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_plugin_page() ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script(
			'wbcom-redirect-admin',
			WBCOM_REDIRECT_PLUGIN_URL . 'admin/assets/js/wbcom-redirect-admin.js',
			array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-sortable' ),
			$this->version,
			true
		);

		wp_localize_script(
			'wbcom-redirect-admin',
			'wbcomRedirect',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wbcom-redirect-admin' ),
			)
		);
	}

	/**
	 * Hide admin notices on our settings pages.
	 */
	public function hide_admin_notices() {
		$pages = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', $this->plugin_slug );
		$page  = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : ''; // phpcs:ignore

		if ( in_array( $page, $pages, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Check if we're on a plugin settings page.
	 *
	 * @return bool
	 */
	private function is_plugin_page() {
		$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : ''; // phpcs:ignore
		return in_array( $page, array( $this->plugin_slug, 'wbcomplugins' ), true );
	}
}
