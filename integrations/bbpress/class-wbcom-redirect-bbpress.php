<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wbcom_Redirect_bbPress extends Wbcom_Redirect_Integration {

	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	public function get_slug() {
		return 'bbpress';
	}

	public function get_name() {
		return __( 'bbPress', 'bp-redirect' );
	}

	public function is_available() {
		return class_exists( 'bbPress' );
	}

	public function get_destinations() {
		return array(
			new Wbcom_Redirect_Destination( 'bbpress.forums', __( 'Forums', 'bp-redirect' ), 'bbpress' ),
			new Wbcom_Redirect_Destination( 'bbpress.user-profile', __( 'User Profile', 'bp-redirect' ), 'bbpress' ),
		);
	}

	public function resolve_url( $destination_slug, $user ) {
		switch ( $destination_slug ) {
			case 'forums':
				if ( function_exists( 'bbp_get_forums_url' ) ) {
					return bbp_get_forums_url();
				}
				// Fallback: get the forums page.
				$page_id = get_option( '_bbp_root_slug_custom_slug' );
				if ( $page_id ) {
					return get_permalink( $page_id );
				}
				return home_url( '/forums/' );

			case 'user-profile':
				if ( function_exists( 'bbp_get_user_profile_url' ) ) {
					return bbp_get_user_profile_url( $user->ID );
				}
				return false;
		}

		return false;
	}
}

new Wbcom_Redirect_bbPress();
