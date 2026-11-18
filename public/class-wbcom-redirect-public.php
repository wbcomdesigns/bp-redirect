<?php
/**
 * Front-end redirect handling for login and logout.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_Public
 *
 * Hooks into WordPress login_redirect and logout_redirect filters
 * and delegates URL resolution to the priority-chain resolver.
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_Public {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * URL resolver instance.
	 *
	 * @var Wbcom_Redirect_Resolver
	 */
	private $resolver;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name Plugin slug.
	 * @param string $version     Plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->resolver    = new Wbcom_Redirect_Resolver();
	}

	/**
	 * Filter: login_redirect
	 *
	 * @param string  $redirect_to Default redirect URL.
	 * @param string  $request     Requested redirect URL.
	 * @param WP_User $user        User object or WP_Error.
	 * @return string
	 */
	public function handle_login_redirect( $redirect_to, $request, $user ) {
		if ( is_wp_error( $user ) || empty( $user ) ) {
			return $redirect_to;
		}

		/**
		 * Filters whether a requested same-site redirect_to beats the configured login redirect.
		 *
		 * By default the configured rule always wins, so a member who logs in from
		 * wp-login.php?redirect_to=... still lands on the rule's destination. Return true
		 * to send the member back to the page they came from instead. Off-site
		 * redirect_to values are never followed.
		 *
		 * @since 2.3.0
		 *
		 * @param bool    $honor   Whether to follow the requested URL. Default false.
		 * @param string  $request The redirect_to URL the visitor asked for.
		 * @param WP_User $user    The member logging in.
		 */
		// wp-login.php posts redirect_to=admin_url() on every plain login; that is not a real request.
		$is_default = untrailingslashit( $request ) === untrailingslashit( admin_url() );
		if ( $request && ! $is_default && apply_filters( 'wbcom_redirect_honor_redirect_to', false, $request, $user ) ) {
			$host = wp_parse_url( $request, PHP_URL_HOST );
			if ( wp_validate_redirect( $request, '' ) && ( ! $host || wp_parse_url( home_url(), PHP_URL_HOST ) === $host ) ) {
				return $request;
			}
		}

		return $this->allow( $this->resolver->resolve_login( $user, $redirect_to ), $redirect_to );
	}

	/**
	 * Filter: woocommerce_login_redirect (the My Account login form skips login_redirect).
	 *
	 * @since 2.3.0
	 *
	 * @param string  $redirect_to Default redirect URL.
	 * @param WP_User $user        User object.
	 * @return string
	 */
	public function handle_woocommerce_login_redirect( $redirect_to, $user ) {
		return $this->handle_login_redirect( $redirect_to, $redirect_to, $user );
	}

	/**
	 * Filter: logout_redirect
	 *
	 * @param string  $redirect_to Default redirect URL.
	 * @param string  $request     Requested redirect URL.
	 * @param WP_User $user        User object or WP_Error.
	 * @return string
	 */
	public function handle_logout_redirect( $redirect_to, $request, $user ) {
		if ( is_wp_error( $user ) || empty( $user ) ) {
			return $redirect_to;
		}

		return $this->allow( $this->resolver->resolve_logout( $user, $redirect_to ), $redirect_to );
	}

	/**
	 * Let WordPress follow a destination the site admin configured.
	 *
	 * WordPress sends every login/logout through wp_safe_redirect(), which silently
	 * swaps any other host for the dashboard. A Custom URL on another domain is an admin
	 * choice, so its host is allowed for this request only. The URL stays raw: entity
	 * encoding would turn "&" in a query string into "&#038;".
	 *
	 * Only a destination this plugin resolved is allowed. When no rule matches, the
	 * resolver hands back WordPress' own redirect_to, which can come from the visitor's
	 * request, so it keeps the normal safe-redirect check.
	 *
	 * @since 2.3.0
	 *
	 * @param string|false $url         Resolved destination.
	 * @param string       $redirect_to WordPress' default destination.
	 * @return string
	 */
	private function allow( $url, $redirect_to ) {
		if ( ! $url || $url === $redirect_to ) {
			return $redirect_to;
		}
		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( $host ) {
			add_filter(
				'allowed_redirect_hosts',
				static function ( $hosts ) use ( $host ) {
					$hosts[] = $host;
					return $hosts;
				}
			);
		}
		return $url;
	}
}
