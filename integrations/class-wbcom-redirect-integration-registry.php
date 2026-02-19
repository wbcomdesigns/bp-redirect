<?php
/**
 * Singleton registry for redirect integrations.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_Integration_Registry
 *
 * Singleton that stores all registered integrations and provides
 * lookup helpers for active integrations, destinations, and admin tabs.
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_Integration_Registry {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Registered integration instances keyed by slug.
	 *
	 * @var Wbcom_Redirect_Integration[]
	 */
	private $integrations = array();

	/**
	 * Private constructor — use instance() instead.
	 */
	private function __construct() {}

	/**
	 * Get the singleton instance.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register an integration.
	 *
	 * @param Wbcom_Redirect_Integration $integration Integration instance.
	 */
	public function register( Wbcom_Redirect_Integration $integration ) {
		$this->integrations[ $integration->get_slug() ] = $integration;
	}

	/**
	 * Get a specific integration by slug.
	 *
	 * @param string $slug Integration slug.
	 * @return Wbcom_Redirect_Integration|null
	 */
	public function get( $slug ) {
		return isset( $this->integrations[ $slug ] ) ? $this->integrations[ $slug ] : null;
	}

	/**
	 * Get all registered integrations (available or not).
	 *
	 * @return Wbcom_Redirect_Integration[]
	 */
	public function get_all() {
		return $this->integrations;
	}

	/**
	 * Get only active (available) integrations.
	 *
	 * @return Wbcom_Redirect_Integration[]
	 */
	public function get_active() {
		return array_filter(
			$this->integrations,
			function ( $integration ) {
				return $integration->is_available();
			}
		);
	}

	/**
	 * Get all destinations from all active integrations.
	 *
	 * @return Wbcom_Redirect_Destination[]
	 */
	public function get_all_destinations() {
		$destinations = array();
		foreach ( $this->get_active() as $integration ) {
			$destinations = array_merge( $destinations, $integration->get_destinations() );
		}
		return $destinations;
	}

	/**
	 * Get integrations that have group types (need their own admin tab).
	 *
	 * @return Wbcom_Redirect_Integration[]
	 */
	public function get_with_admin_tabs() {
		return array_filter(
			$this->get_active(),
			function ( $integration ) {
				return $integration->has_admin_tab();
			}
		);
	}
}
