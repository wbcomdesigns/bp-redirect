<?php
/**
 * LearnDash integration — Student Dashboard and Courses destinations.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_LearnDash
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_LearnDash extends Wbcom_Redirect_Integration {

	/**
	 * Constructor — self-registers with the integration registry.
	 */
	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	/** {@inheritDoc} */
	public function get_slug() {
		return 'learndash';
	}

	/** {@inheritDoc} */
	public function get_name() {
		return __( 'LearnDash', 'bp-redirect' );
	}

	/** {@inheritDoc} */
	public function is_available() {
		return defined( 'LEARNDASH_VERSION' ) || class_exists( 'SFWD_LMS' );
	}

	/** {@inheritDoc} */
	public function get_destinations() {
		$destinations = array();
		// LearnDash has no dashboard page of its own; offer it only when the site has a published "dashboard" page.
		if ( $this->get_dashboard_url() ) {
			$destinations[] = new Wbcom_Redirect_Destination( 'learndash.dashboard', __( 'Student Dashboard', 'bp-redirect' ), 'learndash' );
		}
		$destinations[] = new Wbcom_Redirect_Destination( 'learndash.courses', __( 'Courses', 'bp-redirect' ), 'learndash' );
		return $destinations;
	}

	/**
	 * URL of the published page with the slug "dashboard", if there is one.
	 *
	 * @return string|false
	 */
	private function get_dashboard_url() {
		$page = get_page_by_path( 'dashboard' );
		return ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : false;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string  $destination_slug The destination slug.
	 * @param WP_User $user             The user object.
	 */
	public function resolve_url( $destination_slug, $user ) {
		switch ( $destination_slug ) {
			case 'dashboard':
				return $this->get_dashboard_url();

			case 'courses':
				$archive = get_post_type_archive_link( 'sfwd-courses' );
				return $archive ? $archive : false;
		}

		return false;
	}

	/** {@inheritDoc} */
	public function get_page_id( $destination_slug ) {
		if ( 'dashboard' !== $destination_slug ) {
			return 0; // Courses is a post type archive, not a page.
		}
		$page = get_page_by_path( 'dashboard' );
		return $page ? (int) $page->ID : -1;
	}
}

new Wbcom_Redirect_LearnDash();
