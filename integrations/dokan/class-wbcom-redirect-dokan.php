<?php
/**
 * Dokan integration — Vendor Dashboard and Store Page destinations.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_Dokan
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_Dokan extends Wbcom_Redirect_Integration {

	/**
	 * Constructor — self-registers with the integration registry.
	 */
	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	/** {@inheritDoc} */
	public function get_slug() {
		return 'dokan';
	}

	/** {@inheritDoc} */
	public function get_name() {
		return __( 'Dokan', 'bp-redirect' );
	}

	/** {@inheritDoc} */
	public function is_available() {
		return class_exists( 'WeDevs_Dokan' ) || function_exists( 'dokan' );
	}

	/** {@inheritDoc} */
	public function get_destinations() {
		return array(
			new Wbcom_Redirect_Destination( 'dokan.dashboard', __( 'Vendor Dashboard', 'bp-redirect' ), 'dokan' ),
			new Wbcom_Redirect_Destination( 'dokan.store', __( 'Store Page', 'bp-redirect' ), 'dokan' ),
		);
	}

	/** {@inheritDoc} */
	public function resolve_url( $destination_slug, $user ) {
		switch ( $destination_slug ) {
			case 'dashboard':
				if ( function_exists( 'dokan_get_navigation_url' ) ) {
					return dokan_get_navigation_url();
				}
				return false;

			case 'store':
				if ( function_exists( 'dokan_get_store_url' ) ) {
					return dokan_get_store_url( $user->ID );
				}
				return false;
		}

		return false;
	}
}

new Wbcom_Redirect_Dokan();
