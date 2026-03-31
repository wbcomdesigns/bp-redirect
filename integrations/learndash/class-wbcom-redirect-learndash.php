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
		return array(
			new Wbcom_Redirect_Destination( 'learndash.dashboard', __( 'Student Dashboard', 'bp-redirect' ), 'learndash' ),
			new Wbcom_Redirect_Destination( 'learndash.courses', __( 'Courses', 'bp-redirect' ), 'learndash' ),
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
			case 'dashboard':
				// LearnDash 4.x+ stores the dashboard page ID.
				if ( function_exists( 'learndash_get_page_id' ) ) {
					$page_id = learndash_get_page_id( 'dashboard' );
					if ( $page_id ) {
						return get_permalink( $page_id );
					}
				}
				// Fallback: check the pages_wp settings.
				$pages_settings = get_option( 'learndash_settings_pages_wp', array() );
				if ( ! empty( $pages_settings['dashboard'] ) ) {
					return get_permalink( absint( $pages_settings['dashboard'] ) );
				}
				return false;

			case 'courses':
				if ( function_exists( 'learndash_get_course_list' ) ) {
					$courses_page = get_option( 'learndash_settings_courses_cpt' );
					if ( ! empty( $courses_page['has_archive'] ) ) {
						return get_post_type_archive_link( 'sfwd-courses' );
					}
				}
				// Fallback.
				$archive = get_post_type_archive_link( 'sfwd-courses' );
				return $archive ? $archive : false;
		}

		return false;
	}
}

new Wbcom_Redirect_LearnDash();
