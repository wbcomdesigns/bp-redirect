<?php
/**
 * Core orchestrator for the Login Logout Redirect plugin.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect
 *
 * Loads dependencies, registers hooks, and bootstraps integrations.
 *
 * @since 2.1.0
 */
class Wbcom_Redirect {

	/**
	 * Hook loader instance.
	 *
	 * @var Wbcom_Redirect_Loader
	 */
	protected $loader;

	/**
	 * Plugin slug used for text-domain and admin pages.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * Current plugin version.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Constructor — loads dependencies and defines hooks.
	 */
	public function __construct() {
		$this->plugin_name = 'bp-redirect';
		$this->version     = WBCOM_REDIRECT_VERSION;

		$this->load_dependencies();
		$this->load_integrations();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Require all core class files.
	 */
	private function load_dependencies() {
		$path = WBCOM_REDIRECT_PLUGIN_PATH;

		require_once $path . 'includes/class-wbcom-redirect-loader.php';
		require_once $path . 'includes/class-wbcom-redirect-destination.php';
		require_once $path . 'includes/class-wbcom-redirect-resolver.php';
		require_once $path . 'includes/functions.php';

		// Integration framework.
		require_once $path . 'integrations/class-wbcom-redirect-integration.php';
		require_once $path . 'integrations/class-wbcom-redirect-integration-registry.php';

		// Admin.
		require_once $path . 'admin/class-wbcom-redirect-admin.php';

		// Public.
		require_once $path . 'public/class-wbcom-redirect-public.php';

		// Wbcom admin wrapper.
		require_once $path . 'admin/wbcom/class-wbcom-admin-settings.php';

		$this->loader = new Wbcom_Redirect_Loader();
	}

	/**
	 * Load and register integration modules.
	 */
	private function load_integrations() {
		$path     = WBCOM_REDIRECT_PLUGIN_PATH . 'integrations/';
		$registry = Wbcom_Redirect_Integration_Registry::instance();

		$integrations = array(
			'buddypress'  => 'buddypress/class-wbcom-redirect-buddypress.php',
			'woocommerce' => 'woocommerce/class-wbcom-redirect-woocommerce.php',
			'bbpress'     => 'bbpress/class-wbcom-redirect-bbpress.php',
			'dokan'       => 'dokan/class-wbcom-redirect-dokan.php',
			'learndash'   => 'learndash/class-wbcom-redirect-learndash.php',
			'peepso'      => 'peepso/class-wbcom-redirect-peepso.php',
		);

		foreach ( $integrations as $slug => $file ) {
			if ( file_exists( $path . $file ) ) {
				require_once $path . $file;
			}
		}
	}

	/**
	 * Register the text-domain loading action.
	 *
	 * Since WordPress 4.6, translations are loaded automatically for plugins
	 * hosted on WordPress.org. Manual load_plugin_textdomain() is no longer needed.
	 */
	private function set_locale() {
		// Intentionally left empty — WordPress handles translations automatically since 4.6.
	}

	/**
	 * Register all admin-side hooks via the loader.
	 */
	private function define_admin_hooks() {
		$admin = new Wbcom_Redirect_Admin( $this->plugin_name, $this->version );

		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $admin, 'init_settings' );
		$this->loader->add_action( 'admin_menu', $admin, 'add_menu' );
		$this->loader->add_action( 'wp_ajax_wbcom_redirect_save_settings', $admin, 'ajax_save_settings' );
		$this->loader->add_action( 'in_admin_header', $admin, 'hide_admin_notices' );
	}

	/**
	 * Register front-end redirect filter hooks.
	 */
	private function define_public_hooks() {
		$public = new Wbcom_Redirect_Public( $this->plugin_name, $this->version );

		$this->loader->add_filter( 'login_redirect', $public, 'handle_login_redirect', 10, 3 );
		$this->loader->add_filter( 'logout_redirect', $public, 'handle_logout_redirect', 10, 3 );
	}

	/**
	 * Execute the loader to register all hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Get the plugin slug.
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}
}
