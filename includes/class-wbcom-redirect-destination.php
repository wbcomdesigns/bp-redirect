<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Value object representing a redirect destination provided by an integration.
 * Example: 'buddypress.profile' => 'Member Profile'
 */
class Wbcom_Redirect_Destination {

	/** @var string Unique slug like 'buddypress.profile' */
	private $slug;

	/** @var string Human-readable label. */
	private $label;

	/** @var string Integration slug that owns this destination. */
	private $integration;

	public function __construct( $slug, $label, $integration ) {
		$this->slug        = $slug;
		$this->label       = $label;
		$this->integration = $integration;
	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_label() {
		return $this->label;
	}

	public function get_integration() {
		return $this->integration;
	}
}
