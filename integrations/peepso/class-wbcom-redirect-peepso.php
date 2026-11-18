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
		$destinations = array();
		if ( $this->peepso_page( 'profile' ) ) {
			$destinations[] = new Wbcom_Redirect_Destination( 'peepso.profile', __( 'Profile', 'bp-redirect' ), 'peepso' );
		}
		if ( $this->peepso_page( 'activity' ) ) {
			$destinations[] = new Wbcom_Redirect_Destination( 'peepso.activity', __( 'Activity Stream', 'bp-redirect' ), 'peepso' );
		}
		return $destinations;
	}

	/**
	 * PeepSo's configured page for $name, if it is a published page that renders PeepSo.
	 *
	 * PeepSo builds its links from a stored slug without checking it. Next to BuddyPress the
	 * slug "activity" belongs to BuddyPress, so PeepSo's link opens the BuddyPress stream.
	 * The slug is read from PeepSo's stored config because PeepSo::get_option() returns
	 * false in wp-admin until PeepSo's own config class has loaded.
	 *
	 * @param string $name PeepSo page key: profile or activity.
	 * @return WP_Post|false
	 */
	private function peepso_page( $name ) {
		$config = get_option( 'peepso_config', array() );
		$slug   = isset( $config[ 'page_' . $name ] ) ? trim( (string) $config[ 'page_' . $name ], '/' ) : '';
		$page   = '' === $slug ? get_post( (int) get_option( 'page_on_front' ) ) : get_page_by_path( $slug ); // Empty slug: PeepSo is the front page.
		// A plain string check: PeepSo registers its shortcodes on the front end only, so has_shortcode() fails in wp-admin.
		return ( $page && 'publish' === $page->post_status && false !== strpos( $page->post_content, '[peepso_' . $name ) ) ? $page : false;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string  $destination_slug The destination slug.
	 * @param WP_User $user             The user object.
	 */
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
				return get_permalink( $this->peepso_page( 'activity' ) );
		}

		return false;
	}

	/** {@inheritDoc} */
	public function get_page_id( $destination_slug ) {
		if ( ! in_array( $destination_slug, array( 'profile', 'activity' ), true ) ) {
			return 0;
		}
		$page = $this->peepso_page( $destination_slug );
		return $page ? (int) $page->ID : -1;
	}
}

new Wbcom_Redirect_PeepSo();
