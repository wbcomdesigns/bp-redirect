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
		return $this->is_buddyboss() ? 'BuddyBoss' : 'BuddyPress';
	}

	/**
	 * BuddyPress calls them Member Types, BuddyBoss calls them Profile Types.
	 *
	 * @since 2.3.0
	 * @return array
	 */
	public function get_group_type_labels() {
		if ( $this->is_buddyboss() ) {
			return array(
				'plural'   => __( 'Profile Types', 'bp-redirect' ),
				'singular' => __( 'Profile Type', 'bp-redirect' ),
			);
		}
		return array(
			'plural'   => __( 'Member Types', 'bp-redirect' ),
			'singular' => __( 'Member Type', 'bp-redirect' ),
		);
	}

	/**
	 * Whether the BuddyPress API comes from BuddyBoss Platform.
	 *
	 * @return bool
	 */
	private function is_buddyboss() {
		return defined( 'BP_PLATFORM_VERSION' );
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
			// A type registered without labels still needs a name the owner can recognise.
			$labels          = isset( $type->labels ) ? (array) $type->labels : array();
			$result[ $slug ] = ! empty( $labels['singular_name'] ) ? $labels['singular_name'] : ( ! empty( $labels['name'] ) ? $labels['name'] : $slug );
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
				// A rule saved while the component was on must not lead to a 404 once it is off.
				if ( ! bp_is_active( 'activity' ) ) {
					return false;
				}
				$profile = $this->get_user_profile_url( $user );
				return $profile ? trailingslashit( $profile . 'activity' ) : false;

			case 'groups':
				if ( ! bp_is_active( 'groups' ) ) {
					return false;
				}
				if ( function_exists( 'bp_get_groups_directory_url' ) ) {
					return bp_get_groups_directory_url();
				}
				return function_exists( 'bp_get_groups_directory_permalink' ) ? bp_get_groups_directory_permalink() : false;
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
