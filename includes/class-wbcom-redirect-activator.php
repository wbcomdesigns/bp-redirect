<?php
/**
 * Plugin activation handler.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_Activator
 *
 * Sets default option values on first activation.
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_Activator {

	/**
	 * Run activation tasks — set default options if not already present.
	 */
	public static function activate() {
		// Set fresh defaults — no backward compat needed.
		if ( false === get_option( 'wbcom_redirect_global' ) ) {
			update_option(
				'wbcom_redirect_global',
				array(
					'enabled' => 'no',
					'login'   => array(
						'type'        => 'none',
						'page_id'     => 0,
						'custom_url'  => '',
						'integration' => '',
					),
					'logout'  => array(
						'type'        => 'none',
						'page_id'     => 0,
						'custom_url'  => '',
						'integration' => '',
					),
				)
			);
		}

		if ( false === get_option( 'wbcom_redirect_roles' ) ) {
			update_option(
				'wbcom_redirect_roles',
				array(
					'enabled' => 'yes',
					'roles'   => array(),
				)
			);
		}
	}
}
