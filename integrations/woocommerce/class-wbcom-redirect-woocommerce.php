<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wbcom_Redirect_WooCommerce extends Wbcom_Redirect_Integration {

	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	public function get_slug() {
		return 'woocommerce';
	}

	public function get_name() {
		return __( 'WooCommerce', 'bp-redirect' );
	}

	public function is_available() {
		return class_exists( 'WooCommerce' );
	}

	public function get_destinations() {
		return array(
			new Wbcom_Redirect_Destination( 'woocommerce.my-account', __( 'My Account', 'bp-redirect' ), 'woocommerce' ),
			new Wbcom_Redirect_Destination( 'woocommerce.shop', __( 'Shop', 'bp-redirect' ), 'woocommerce' ),
			new Wbcom_Redirect_Destination( 'woocommerce.checkout', __( 'Checkout', 'bp-redirect' ), 'woocommerce' ),
			new Wbcom_Redirect_Destination( 'woocommerce.orders', __( 'Orders', 'bp-redirect' ), 'woocommerce' ),
		);
	}

	public function resolve_url( $destination_slug, $user ) {
		switch ( $destination_slug ) {
			case 'my-account':
				return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : false;

			case 'shop':
				return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : false;

			case 'checkout':
				return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'checkout' ) : false;

			case 'orders':
				if ( function_exists( 'wc_get_account_endpoint_url' ) ) {
					return wc_get_account_endpoint_url( 'orders' );
				}
				return false;
		}

		return false;
	}
}

new Wbcom_Redirect_WooCommerce();
