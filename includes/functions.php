<?php
/**
 * Helper functions: asset loader and v2.0.0 migration.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get asset filename with intelligent fallback for minified/RTL variants.
 *
 * @param string $type     Asset directory path relative to plugin root (e.g. 'admin/assets/css').
 * @param string $filename Base filename without extension.
 * @return string|false Full relative path or false if not found.
 */
function wbcom_redirect_get_asset_filename( $type, $filename ) {
	$css_dirs     = array( 'admin/assets/css', 'admin/wbcom/assets/css' );
	$use_minified = ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
	$is_rtl       = in_array( $type, $css_dirs, true ) ? is_rtl() : false;
	$base_dir     = WBCOM_REDIRECT_PLUGIN_PATH . $type . '/';

	$variants = array();

	if ( in_array( $type, $css_dirs, true ) ) {
		if ( $is_rtl && $use_minified ) {
			$variants[] = $filename . '-rtl.min.css';
			$variants[] = $filename . '-rtl.css';
		} elseif ( $is_rtl ) {
			$variants[] = $filename . '-rtl.css';
		} elseif ( $use_minified ) {
			$variants[] = $filename . '.min.css';
			$variants[] = $filename . '.css';
		} else {
			$variants[] = $filename . '.css';
		}
	} elseif ( $use_minified ) {
		$variants[] = $filename . '.min.js';
		$variants[] = $filename . '.js';
	} else {
		$variants[] = $filename . '.js';
	}

	foreach ( $variants as $variant ) {
		if ( file_exists( $base_dir . $variant ) ) {
			return $type . '/' . $variant;
		}
	}

	return false;
}

// Backward compat wrapper used by wbcom admin settings.
if ( ! function_exists( 'bp_redirect_get_asset_filename' ) ) {
	/**
	 * Backward-compatible wrapper for wbcom_redirect_get_asset_filename().
	 *
	 * @param string $type     Asset directory path relative to plugin root.
	 * @param string $filename Base filename without extension.
	 * @return string|false Full relative path or false if not found.
	 */
	function bp_redirect_get_asset_filename( $type, $filename ) {
		return wbcom_redirect_get_asset_filename( $type, $filename );
	}
}

/**
 * Migrate v2.0.0 settings to v2.1.0 format.
 * Runs once on admin_init if old options exist and new ones don't.
 */
function wbcom_redirect_maybe_migrate() {
	// Skip if new settings already exist.
	if ( false !== get_option( 'wbcom_redirect_migrated' ) ) {
		return;
	}

	$old_roles  = get_option( 'bp_redirect_admin_settings', false );
	$old_global = get_option( 'bp_redirect_admin_settings_global', false );
	$old_member = get_option( 'bp_redirect_member_type_admin_settings', false );

	// Nothing to migrate.
	if ( false === $old_roles && false === $old_global && false === $old_member ) {
		update_option( 'wbcom_redirect_migrated', '1' );
		return;
	}

	// Migrate global settings.
	if ( $old_global && is_array( $old_global ) ) {
		$new_global = array(
			'enabled' => isset( $old_global['role_btn_value'] ) ? $old_global['role_btn_value'] : 'no',
			'login'   => wbcom_redirect_migrate_config(
				isset( $old_global['bp_login_redirect_settings_global']['global'] ) ? $old_global['bp_login_redirect_settings_global']['global'] : array(),
				'login'
			),
			'logout'  => wbcom_redirect_migrate_config(
				isset( $old_global['bp_logout_redirect_settings_global']['global'] ) ? $old_global['bp_logout_redirect_settings_global']['global'] : array(),
				'logout'
			),
		);
		update_option( 'wbcom_redirect_global', $new_global );
	}

	// Migrate role settings.
	if ( $old_roles && is_array( $old_roles ) ) {
		$new_roles = array(
			'enabled' => isset( $old_roles['role_btn_value'] ) ? $old_roles['role_btn_value'] : 'no',
			'roles'   => array(),
		);

		$login_settings  = isset( $old_roles['bp_login_redirect_settings'] ) ? $old_roles['bp_login_redirect_settings'] : array();
		$logout_settings = isset( $old_roles['bp_logout_redirect_settings'] ) ? $old_roles['bp_logout_redirect_settings'] : array();

		$all_roles = array_unique( array_merge( array_keys( $login_settings ), array_keys( $logout_settings ) ) );
		foreach ( $all_roles as $role ) {
			$new_roles['roles'][ $role ] = array(
				'login'  => wbcom_redirect_migrate_config(
					isset( $login_settings[ $role ] ) ? $login_settings[ $role ] : array(),
					'login'
				),
				'logout' => wbcom_redirect_migrate_config(
					isset( $logout_settings[ $role ] ) ? $logout_settings[ $role ] : array(),
					'logout'
				),
			);
		}
		update_option( 'wbcom_redirect_roles', $new_roles );
	}

	// Migrate BuddyPress member type settings.
	if ( $old_member && is_array( $old_member ) ) {
		$new_bp = array(
			'enabled' => isset( $old_member['member_type_btn_value'] ) ? $old_member['member_type_btn_value'] : 'no',
			'groups'  => array(),
		);

		$login_settings  = isset( $old_member['bp_login_redirect_settings'] ) ? $old_member['bp_login_redirect_settings'] : array();
		$logout_settings = isset( $old_member['bp_logout_redirect_settings'] ) ? $old_member['bp_logout_redirect_settings'] : array();

		$all_types = array_unique( array_merge( array_keys( $login_settings ), array_keys( $logout_settings ) ) );
		foreach ( $all_types as $type ) {
			$new_bp['groups'][ $type ] = array(
				'login'  => wbcom_redirect_migrate_config(
					isset( $login_settings[ $type ] ) ? $login_settings[ $type ] : array(),
					'login'
				),
				'logout' => wbcom_redirect_migrate_config(
					isset( $logout_settings[ $type ] ) ? $logout_settings[ $type ] : array(),
					'logout'
				),
			);
		}
		update_option( 'wbcom_redirect_buddypress', $new_bp );
	}

	update_option( 'wbcom_redirect_migrated', '1' );
}

/**
 * Convert a single old redirect config to the new format.
 *
 * @param array  $old_config Old config array.
 * @param string $context    'login' or 'logout'.
 * @return array New config shape.
 */
function wbcom_redirect_migrate_config( $old_config, $context ) {
	$new = array(
		'type'        => 'none',
		'page_id'     => 0,
		'custom_url'  => '',
		'integration' => '',
	);

	if ( empty( $old_config ) ) {
		return $new;
	}

	$type_key = ( 'login' === $context ) ? 'login_type' : 'logout_type';
	$old_type = isset( $old_config[ $type_key ] ) ? $old_config[ $type_key ] : 'none';

	if ( 'custom' === $old_type ) {
		$url_key = ( 'login' === $context ) ? 'login_url' : 'logout_url';
		$url     = isset( $old_config[ $url_key ] ) ? $old_config[ $url_key ] : '';
		if ( $url ) {
			// Check if URL matches a page permalink.
			$page_id = url_to_postid( $url );
			if ( $page_id ) {
				$new['type']    = 'page';
				$new['page_id'] = $page_id;
			} else {
				$new['type']       = 'custom_url';
				$new['custom_url'] = $url;
			}
		}
	} elseif ( 'referer' === $old_type && 'login' === $context ) {
		$component = isset( $old_config['login_component'] ) ? $old_config['login_component'] : '';
		if ( $component ) {
			$map = array(
				'profile'         => 'buddypress.profile',
				'member_activity' => 'buddypress.activity',
				'groups'          => 'buddypress.groups',
			);
			if ( isset( $map[ $component ] ) ) {
				$new['type']        = 'integration';
				$new['integration'] = $map[ $component ];
			}
		}
	}

	return $new;
}

add_action( 'admin_init', 'wbcom_redirect_maybe_migrate' );
