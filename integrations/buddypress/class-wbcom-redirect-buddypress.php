<?php
/**
 * BuddyPress integration — Profile, Activity, Groups destinations + Member Types.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_BuddyPress
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_BuddyPress extends Wbcom_Redirect_Integration {

	/**
	 * Constructor — self-registers with the integration registry.
	 */
	public function __construct() {
		Wbcom_Redirect_Integration_Registry::instance()->register( $this );
	}

	/** {@inheritDoc} */
	public function get_slug() {
		return 'buddypress';
	}

	/** {@inheritDoc} */
	public function get_name() {
		return __( 'BuddyPress', 'bp-redirect' );
	}

	/** {@inheritDoc} */
	public function is_available() {
		return class_exists( 'BuddyPress' ) || function_exists( 'buddypress' );
	}

	/** {@inheritDoc} */
	public function get_destinations() {
		$destinations = array();

		$destinations[] = new Wbcom_Redirect_Destination(
			'buddypress.profile',
			__( 'Member Profile', 'bp-redirect' ),
			'buddypress'
		);

		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) ) {
			$destinations[] = new Wbcom_Redirect_Destination(
				'buddypress.activity',
				__( 'Member Activity', 'bp-redirect' ),
				'buddypress'
			);
		}

		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
			$destinations[] = new Wbcom_Redirect_Destination(
				'buddypress.groups',
				__( 'Groups Directory', 'bp-redirect' ),
				'buddypress'
			);
		}

		return $destinations;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param WP_User|null $user The user object.
	 */
	public function get_group_types( $user = null ) {
		if ( ! $user || ! function_exists( 'bp_get_member_type' ) ) {
			return array();
		}

		$types = bp_get_member_type( $user->ID, false );
		return $types ? (array) $types : array();
	}

	/** {@inheritDoc} */
	public function get_all_group_types() {
		if ( ! function_exists( 'bp_get_member_types' ) ) {
			return array();
		}

		$types  = bp_get_member_types( array(), 'objects' );
		$result = array();
		foreach ( $types as $slug => $type ) {
			$result[ $slug ] = isset( $type->labels['singular_name'] ) ? $type->labels['singular_name'] : $slug;
		}
		return $result;
	}

	/** {@inheritDoc} */
	public function has_admin_tab() {
		return $this->is_available() && ! empty( $this->get_all_group_types() );
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
				return $this->get_user_profile_url( $user );

			case 'activity':
				$profile = $this->get_user_profile_url( $user );
				return $profile ? trailingslashit( $profile . 'activity' ) : false;

			case 'groups':
				$bp_pages = get_option( 'bp-pages', array() );
				if ( isset( $bp_pages['groups'] ) ) {
					return get_permalink( $bp_pages['groups'] );
				}
				return false;
		}

		return false;
	}

	/**
	 * Get a user's BuddyPress profile URL with backward compatibility.
	 *
	 * @param WP_User $user User object.
	 * @return string|false
	 */
	private function get_user_profile_url( $user ) {
		if ( function_exists( 'bp_members_get_user_url' ) ) {
			return bp_members_get_user_url( $user->ID );
		}
		if ( function_exists( 'bp_core_get_user_domain' ) ) {
			return bp_core_get_user_domain( $user->ID );
		}
		return false;
	}
}

new Wbcom_Redirect_BuddyPress();
