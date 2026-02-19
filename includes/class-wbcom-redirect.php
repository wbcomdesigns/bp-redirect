<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class Wbcom_Redirect {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		$this->plugin_name = 'bp-redirect';
		$this->version     = WBCOM_REDIRECT_VERSION;

		$this->load_dependencies();
		$this->load_integrations();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->init_updater();
	}

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

	private function set_locale() {
		$this->loader->add_action( 'init', $this, 'load_textdomain' );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'bp-redirect', false, dirname( WBCOM_REDIRECT_PLUGIN_BASENAME ) . '/languages/' );
	}

	private function define_admin_hooks() {
		$admin = new Wbcom_Redirect_Admin( $this->plugin_name, $this->version );

		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $admin, 'init_settings' );
		$this->loader->add_action( 'admin_menu', $admin, 'add_menu' );
		$this->loader->add_action( 'wp_ajax_wbcom_redirect_save_settings', $admin, 'ajax_save_settings' );
		$this->loader->add_action( 'in_admin_header', $admin, 'hide_admin_notices' );
	}

	private function define_public_hooks() {
		$public = new Wbcom_Redirect_Public( $this->plugin_name, $this->version );

		$this->loader->add_filter( 'login_redirect', $public, 'handle_login_redirect', 10, 3 );
		$this->loader->add_filter( 'logout_redirect', $public, 'handle_logout_redirect', 10, 3 );
	}

	private function init_updater() {
		if ( class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			PucFactory::buildUpdateChecker(
				'https://demos.wbcomdesigns.com/exporter/free-plugins/bp-redirect.json',
				WBCOM_REDIRECT_PLUGIN_FILE,
				'bp-redirect'
			);
		}
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_version() {
		return $this->version;
	}
}
