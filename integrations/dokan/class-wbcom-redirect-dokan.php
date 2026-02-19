<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wbcom_Redirect_Dokan extends Wbcom_Redirect_Integration {

	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	public function get_slug() {
		return 'dokan';
	}

	public function get_name() {
		return __( 'Dokan', 'bp-redirect' );
	}

	public function is_available() {
		return class_exists( 'WeDevs_Dokan' ) || function_exists( 'dokan' );
	}

	public function get_destinations() {
		return array(
			new Wbcom_Redirect_Destination( 'dokan.dashboard', __( 'Vendor Dashboard', 'bp-redirect' ), 'dokan' ),
			new Wbcom_Redirect_Destination( 'dokan.store', __( 'Store Page', 'bp-redirect' ), 'dokan' ),
		);
	}

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
