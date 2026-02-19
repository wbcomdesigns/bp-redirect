<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves the redirect URL using the priority chain:
 * User Role > Integration Group Types > Global > WordPress Default
 */
class Wbcom_Redirect_Resolver {

	/**
	 * Resolve login redirect URL for a user.
	 *
	 * @param WP_User $user        The user logging in.
	 * @param string  $redirect_to Default redirect URL.
	 * @return string Resolved URL.
	 */
	public function resolve_login( $user, $redirect_to ) {
		return $this->resolve( 'login', $user, $redirect_to );
	}

	/**
	 * Resolve logout redirect URL for a user.
	 *
	 * @param WP_User $user        The user logging out.
	 * @param string  $redirect_to Default redirect URL.
	 * @return string Resolved URL.
	 */
	public function resolve_logout( $user, $redirect_to ) {
		return $this->resolve( 'logout', $user, $redirect_to );
	}

	/**
	 * Core resolution logic.
	 *
	 * @param string  $context     'login' or 'logout'.
	 * @param WP_User $user        User object.
	 * @param string  $redirect_to Fallback URL.
	 * @return string
	 */
	private function resolve( $context, $user, $redirect_to ) {
		// 1. Try user role redirect.
		$url = $this->try_role_redirect( $context, $user );
		if ( $url ) {
			return $url;
		}

		// 2. Try integration group types (e.g. BuddyPress member types).
		$url = $this->try_integration_group_redirect( $context, $user );
		if ( $url ) {
			return $url;
		}

		// 3. Try global redirect.
		$url = $this->try_global_redirect( $context, $user );
		if ( $url ) {
			return $url;
		}

		// 4. WordPress default.
		return $redirect_to;
	}

	/**
	 * Check role-based redirect settings.
	 *
	 * @param string  $context 'login' or 'logout'.
	 * @param WP_User $user    User object.
	 * @return string|false URL or false.
	 */
	private function try_role_redirect( $context, $user ) {
		$settings = get_option( 'wbcom_redirect_roles', array() );

		if ( empty( $settings['enabled'] ) || 'yes' !== $settings['enabled'] ) {
			return false;
		}

		$roles = ! empty( $settings['roles'] ) ? $settings['roles'] : array();
		if ( empty( $roles ) ) {
			return false;
		}

		$user_roles = $this->get_user_roles( $user );
		foreach ( $user_roles as $role ) {
			if ( isset( $roles[ $role ][ $context ] ) ) {
				$config = $roles[ $role ][ $context ];
				$url    = $this->resolve_config( $config, $user );
				if ( $url ) {
					return $url;
				}
			}
		}

		return false;
	}

	/**
	 * Check integration group type redirects (e.g. BP member types).
	 *
	 * @param string  $context 'login' or 'logout'.
	 * @param WP_User $user    User object.
	 * @return string|false URL or false.
	 */
	private function try_integration_group_redirect( $context, $user ) {
		if ( ! class_exists( 'Wbcom_Redirect_Integration_Registry' ) ) {
			return false;
		}

		$registry     = Wbcom_Redirect_Integration_Registry::instance();
		$integrations = $registry->get_active();

		foreach ( $integrations as $integration ) {
			$group_types = $integration->get_group_types( $user );
			if ( empty( $group_types ) ) {
				continue;
			}

			$option_key = 'wbcom_redirect_' . $integration->get_slug();
			$settings   = get_option( $option_key, array() );

			if ( empty( $settings['enabled'] ) || 'yes' !== $settings['enabled'] ) {
				continue;
			}

			$groups = ! empty( $settings['groups'] ) ? $settings['groups'] : array();

			foreach ( $group_types as $group_slug ) {
				if ( isset( $groups[ $group_slug ][ $context ] ) ) {
					$config = $groups[ $group_slug ][ $context ];
					$url    = $this->resolve_config( $config, $user );
					if ( $url ) {
						return $url;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Check global redirect settings.
	 *
	 * @param string  $context 'login' or 'logout'.
	 * @param WP_User $user    User object.
	 * @return string|false URL or false.
	 */
	private function try_global_redirect( $context, $user ) {
		$settings = get_option( 'wbcom_redirect_global', array() );

		if ( empty( $settings['enabled'] ) || 'yes' !== $settings['enabled'] ) {
			return false;
		}

		if ( ! isset( $settings[ $context ] ) ) {
			return false;
		}

		return $this->resolve_config( $settings[ $context ], $user );
	}

	/**
	 * Resolve a single redirect config to a URL.
	 *
	 * Config shape:
	 *   type: 'none' | 'page' | 'custom_url' | 'integration'
	 *   page_id: int
	 *   custom_url: string
	 *   integration: 'buddypress.profile' | 'woocommerce.my-account' | etc.
	 *
	 * @param array   $config Redirect configuration.
	 * @param WP_User $user   User object.
	 * @return string|false URL or false.
	 */
	public function resolve_config( $config, $user ) {
		if ( empty( $config ) || ! is_array( $config ) ) {
			return false;
		}

		$type = ! empty( $config['type'] ) ? $config['type'] : 'none';

		switch ( $type ) {
			case 'page':
				$page_id = ! empty( $config['page_id'] ) ? absint( $config['page_id'] ) : 0;
				if ( $page_id ) {
					$url = get_permalink( $page_id );
					return $url ? esc_url_raw( $url ) : false;
				}
				return false;

			case 'custom_url':
				$url = ! empty( $config['custom_url'] ) ? trim( $config['custom_url'] ) : '';
				return $url ? esc_url_raw( $url ) : false;

			case 'integration':
				return $this->resolve_integration_destination( $config, $user );

			case 'none':
			default:
				return false;
		}
	}

	/**
	 * Resolve an integration destination slug to a URL.
	 *
	 * @param array   $config Config with 'integration' key like 'buddypress.profile'.
	 * @param WP_User $user   User object.
	 * @return string|false
	 */
	private function resolve_integration_destination( $config, $user ) {
		$destination_slug = ! empty( $config['integration'] ) ? $config['integration'] : '';
		if ( empty( $destination_slug ) ) {
			return false;
		}

		if ( ! class_exists( 'Wbcom_Redirect_Integration_Registry' ) ) {
			return false;
		}

		// Destination format: 'integration_slug.destination_slug'
		$parts = explode( '.', $destination_slug, 2 );
		if ( count( $parts ) !== 2 ) {
			return false;
		}

		$integration_slug  = $parts[0];
		$dest_slug         = $parts[1];
		$registry          = Wbcom_Redirect_Integration_Registry::instance();
		$integration       = $registry->get( $integration_slug );

		if ( ! $integration || ! $integration->is_available() ) {
			return false;
		}

		$url = $integration->resolve_url( $dest_slug, $user );
		return $url ? esc_url_raw( $url ) : false;
	}

	/**
	 * Get user roles.
	 *
	 * @param WP_User $user User object.
	 * @return array
	 */
	private function get_user_roles( $user ) {
		if ( ! $user || ! isset( $user->roles ) ) {
			return array();
		}
		return (array) $user->roles;
	}
}
