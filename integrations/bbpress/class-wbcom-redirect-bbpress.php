<?php
/**
 * BbPress integration — Forums and User Profile destinations.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_bbPress
 *
 * @since 2.1.0
 */
// phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid, Squiz.Commenting.ClassComment.Missing -- BbPress is the correct capitalization; docblock is above.
class Wbcom_Redirect_bbPress extends Wbcom_Redirect_Integration {

	/**
	 * Constructor — self-registers with the integration registry.
	 */
	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	/** {@inheritDoc} */
	public function get_slug() {
		return 'bbpress';
	}

	/** {@inheritDoc} */
	public function get_name() {
		return __( 'bbPress', 'bp-redirect' );
	}

	/** {@inheritDoc} */
	public function is_available() {
		return class_exists( 'bbPress' );
	}

	/** {@inheritDoc} */
	public function get_destinations() {
		return array(
			new Wbcom_Redirect_Destination( 'bbpress.forums', __( 'Forums', 'bp-redirect' ), 'bbpress' ),
			new Wbcom_Redirect_Destination( 'bbpress.user-profile', __( 'User Profile', 'bp-redirect' ), 'bbpress' ),
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string  $destination_slug The destination slug.
	 * @param WP_User $user             The user object.
	 */
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
