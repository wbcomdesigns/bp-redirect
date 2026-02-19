<?php
/**
 * Redirect destination value object.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_Destination
 *
 * Immutable value object representing a redirect destination provided by an integration.
 * Example: 'buddypress.profile' => 'Member Profile'.
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_Destination {

	/**
	 * Unique slug in 'integration.destination' format.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Human-readable label for the admin UI.
	 *
	 * @var string
	 */
	private $label;

	/**
	 * Integration slug that owns this destination.
	 *
	 * @var string
	 */
	private $integration;

	/**
	 * Constructor.
	 *
	 * @param string $slug        Unique slug like 'buddypress.profile'.
	 * @param string $label       Human-readable label.
	 * @param string $integration Parent integration slug.
	 */
	public function __construct( $slug, $label, $integration ) {
		$this->slug        = $slug;
		$this->label       = $label;
		$this->integration = $integration;
	}

	/**
	 * Get the destination slug.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get the human-readable label.
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Get the parent integration slug.
	 *
	 * @return string
	 */
	public function get_integration() {
		return $this->integration;
	}
}
