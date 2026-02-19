<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wbcom_Redirect_PeepSo extends Wbcom_Redirect_Integration {

	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	public function get_slug() {
		return 'peepso';
	}

	public function get_name() {
		return __( 'PeepSo', 'bp-redirect' );
	}

	public function is_available() {
		return class_exists( 'PeepSo' );
	}

	public function get_destinations() {
		return array(
			new Wbcom_Redirect_Destination( 'peepso.profile', __( 'Profile', 'bp-redirect' ), 'peepso' ),
			new Wbcom_Redirect_Destination( 'peepso.activity', __( 'Activity Stream', 'bp-redirect' ), 'peepso' ),
		);
	}

	public function resolve_url( $destination_slug, $user ) {
		switch ( $destination_slug ) {
			case 'profile':
				if ( class_exists( 'PeepSoUser' ) ) {
					$peepso_user = PeepSoUser::get_instance( $user->ID );
					if ( method_exists( $peepso_user, 'get_profileurl' ) ) {
						return $peepso_user->get_profileurl();
					}
				}
				return false;

			case 'activity':
				if ( class_exists( 'PeepSo' ) && method_exists( 'PeepSo', 'get_page' ) ) {
					return PeepSo::get_page( 'activity' );
				}
				return false;
		}

		return false;
	}
}

new Wbcom_Redirect_PeepSo();
