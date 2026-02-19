<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wbcom_Redirect_LearnDash extends Wbcom_Redirect_Integration {

	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	public function get_slug() {
		return 'learndash';
	}

	public function get_name() {
		return __( 'LearnDash', 'bp-redirect' );
	}

	public function is_available() {
		return defined( 'LEARNDASH_VERSION' ) || class_exists( 'SFWD_LMS' );
	}

	public function get_destinations() {
		return array(
			new Wbcom_Redirect_Destination( 'learndash.dashboard', __( 'Student Dashboard', 'bp-redirect' ), 'learndash' ),
			new Wbcom_Redirect_Destination( 'learndash.courses', __( 'Courses', 'bp-redirect' ), 'learndash' ),
		);
	}

	public function resolve_url( $destination_slug, $user ) {
		switch ( $destination_slug ) {
			case 'dashboard':
				// LearnDash 4.x+ has a dashboard page setting.
				$page_id = get_option( 'learndash_settings_custom_labels' );
				// Fallback: try common slug.
				$dashboard_page = get_page_by_path( 'dashboard' );
				if ( $dashboard_page ) {
					return get_permalink( $dashboard_page->ID );
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
