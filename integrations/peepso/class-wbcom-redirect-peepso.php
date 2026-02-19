<?php
/**
 * PeepSo integration — Profile and Activity Stream destinations.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_PeepSo
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_PeepSo extends Wbcom_Redirect_Integration {

	/**
	 * Constructor — self-registers with the integration registry.
	 */
	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	/** {@inheritDoc} */
	public function get_slug() {
		return 'peepso';
	}

	/** {@inheritDoc} */
	public function get_name() {
		return __( 'PeepSo', 'bp-redirect' );
	}

	/** {@inheritDoc} */
	public function is_available() {
		return class_exists( 'PeepSo' );
	}

	/** {@inheritDoc} */
	public function get_destinations() {
		return array(
			new Wbcom_Redirect_Destination( 'peepso.profile', __( 'Profile', 'bp-redirect' ), 'peepso' ),
			new Wbcom_Redirect_Destination( 'peepso.activity', __( 'Activity Stream', 'bp-redirect' ), 'peepso' ),
		);
	}

	/** {@inheritDoc} */
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
