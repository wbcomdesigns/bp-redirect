<?php
/**
 * Abstract base class for redirect integrations.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract base class for all integrations.
 *
 * Each integration (BuddyPress, WooCommerce, etc.) extends this class
 * and self-registers with the registry.
 */
abstract class Wbcom_Redirect_Integration {

	/**
	 * Unique slug (e.g. 'buddypress', 'woocommerce').
	 *
	 * @return string
	 */
	abstract public function get_slug();

	/**
	 * Human-readable display name.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Whether this integration's parent plugin is active.
	 *
	 * @return bool
	 */
	abstract public function is_available();

	/**
	 * Return redirect destinations provided by this integration.
	 *
	 * @return Wbcom_Redirect_Destination[]
	 */
	abstract public function get_destinations();

	/**
	 * Return user group types for this integration (e.g. BP member types).
	 * Most integrations return empty array (no group types).
	 *
	 * @param WP_User $user User object.
	 * @return array Array of group type slugs the user belongs to.
	 */
	public function get_group_types( $user = null ) {
		return array();
	}

	/**
	 * Return all available group type definitions for admin UI.
	 *
	 * @return array Associative array of slug => label.
	 */
	public function get_all_group_types() {
		return array();
	}

	/**
	 * Whether this integration has group types that need their own admin tab.
	 *
	 * @return bool
	 */
	public function has_admin_tab() {
		return ! empty( $this->get_all_group_types() );
	}

	/**
	 * Resolve a destination slug to a URL for the given user.
	 *
	 * @param string  $destination_slug The destination slug (e.g. 'profile', 'my-account').
	 * @param WP_User $user             User object.
	 * @return string|false URL or false if cannot resolve.
	 */
	abstract public function resolve_url( $destination_slug, $user );
}
