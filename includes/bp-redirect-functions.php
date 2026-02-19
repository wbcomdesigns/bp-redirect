<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update member type data on new keys for exisiting setup
 *
 * @return void
 */
function bp_redirect_update_member_type_data_on_new_key() {

	// get existing user role and member type data
	$saved_setting = get_option( 'bp_redirect_admin_settings' );
	if ( ! $saved_setting ) {
		return;
	}
	// get all member type
	$terms = get_terms(
		array(
			'taxonomy'   => 'bp_member_type',
			'hide_empty' => false,
		)
	);

	// create array for member type data for save
	$mem_type_setting = array();

	$mem_type_setting = array(
		'bp_login_redirect_settings'  => array(),
		'bp_logout_redirect_settings' => array(),
		'member_type_btn_value'       => '',
		'loginSequence'               => '',
		'logoutSequence'              => '',
	);

	foreach ( $terms as $key => $tm ) {
		if ( isset( $saved_setting['bp_login_redirect_settings'][ $tm->name ] ) ) {
			$mem_type_setting['bp_login_redirect_settings'][ $tm->name ] = array(
				'login_type'      => isset( $saved_setting['bp_login_redirect_settings'][ $tm->name ]['login_type'] ) ? $saved_setting['bp_login_redirect_settings'][ $tm->name ]['login_type'] : '',
				'login_component' => isset( $saved_setting['bp_login_redirect_settings'][ $tm->name ]['login_component'] ) ? $saved_setting['bp_login_redirect_settings'][ $tm->name ]['login_component'] : '',
				'login_url'       => isset( $saved_setting['bp_login_redirect_settings'][ $tm->name ]['login_url'] ) ? $saved_setting['bp_login_redirect_settings'][ $tm->name ]['login_url'] : '',
			);

			$mem_type_setting['bp_logout_redirect_settings'][ $tm->name ] = array(
				'logout_type' => isset( $saved_setting['bp_logout_redirect_settings'][ $tm->name ]['logout_type'] ) ? $saved_setting['bp_logout_redirect_settings'][ $tm->name ]['logout_type'] : '',
				'logout_url'  => isset( $saved_setting['bp_logout_redirect_settings'][ $tm->name ]['logout_url'] ) ? $saved_setting['bp_logout_redirect_settings'][ $tm->name ]['logout_url'] : '',
			);
		}
	}

		$mem_type_setting['member_type_btn_value'] = isset( $saved_setting['member_type_btn_value'] ) ? $saved_setting['member_type_btn_value'] : '';
		$mem_type_setting['loginSequence']         = isset( $saved_setting['loginSequence'] ) ? $saved_setting['loginSequence'] : '';
		$mem_type_setting['logoutSequence']        = isset( $saved_setting['logoutSequence'] ) ? $saved_setting['logoutSequence'] : '';

		// check flag set or not on the existing setup
		$check_mem_type_data = get_option( 'flag_member_type_data' );

		// if flag isnot set then update the member type data on the new keys
	if ( ! ( $check_mem_type_data ) ) {
		update_option( 'bp_redirect_member_type_admin_settings', $mem_type_setting );
		update_option( 'flag_member_type_data', 1 );
	}
}
add_action( 'admin_init', 'bp_redirect_update_member_type_data_on_new_key' );


/**
 * Get asset filename with intelligent fallback
 *
 * @since    1.9.1
 * @param    string $type     Asset type ('css' or 'js')
 * @param    string $filename Base filename without extension
 * @return   string|false     Full filename with path or false if not found
 */
function bp_redirect_get_asset_filename( $type, $filename ) {

	$css_file_paths = array( 'admin/assets/css', 'admin/wbcom/assets/css' );
	// Determine if we should use minified files
	$use_minified = ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
	// Determine if RTL is needed (only for CSS)
	$is_rtl = in_array( $type, $css_file_paths ) ? is_rtl() : false;

	// Build the base directory path
	$base_dir = plugin_dir_path( __DIR__ ) . $type . '/';

	// Array of file variants to try in order of preference
	$variants = array();

	if ( in_array( $type, $css_file_paths ) ) {
		if ( $is_rtl && $use_minified ) {
			$variants[] = $filename . '-rtl.min.css';      // 1st preference: RTL minified
			$variants[] = $filename . '-rtl.css';          // 2nd preference: RTL non-minified
		} elseif ( $is_rtl && ! $use_minified ) {
			$variants[] = $filename . '-rtl.css';          // 1st preference: RTL non-minified
		} elseif ( ! $is_rtl && $use_minified ) {
			$variants[] = $filename . '.min.css';          // 1st preference: LTR minified
			$variants[] = $filename . '.css';              // 2nd preference: LTR non-minified
		} else {
			$variants[] = $filename . '.css';              // 1st preference: LTR non-minified
		}
	} elseif ( $use_minified ) { // JavaScript
			$variants[] = $filename . '.min.js';           // 1st preference: minified
			$variants[] = $filename . '.js';               // 2nd preference: non-minified
	} else {
		$variants[] = $filename . '.js';               // 1st preference: non-minified

	}

	// Check each variant in order
	foreach ( $variants as $variant ) {
		if ( file_exists( $base_dir . $variant ) ) {
			return $type . '/' . $variant;
		}
	}

	// No valid file found
	return false;
}
