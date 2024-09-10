<?php
/**
 * Update member type data on new keys for exisiting setup
 *
 * @return void
 */
function bp_redirect_update_member_type_data_on_new_key(){

	//get existing user role and member type data
	$saved_setting = get_option( 'bp_redirect_admin_settings' );
	if( ! $saved_setting ){
		return;
	}
	// get all member type
	$terms = get_terms(
		array(
			'taxonomy'   => 'bp_member_type',
			'hide_empty' => false,
		)
	);

	//create array for member type data for save
	$mem_type_setting = array();

	$mem_type_setting = [
			'bp_login_redirect_settings' => [],
			'bp_logout_redirect_settings' => [],
			'member_type_btn_value' => '',
			'loginSequence' =>'',
			'logoutSequence' =>'',
		];		

		foreach ( $terms as $key => $tm ) {
			if( isset( $saved_setting['bp_login_redirect_settings'][$tm->name] ) ){				
				$mem_type_setting['bp_login_redirect_settings'][$tm->name] = [
					'login_type' 		=> isset( $saved_setting['bp_login_redirect_settings'][$tm->name]['login_type'] ) ? $saved_setting['bp_login_redirect_settings'][$tm->name]['login_type'] : '',
					'login_component' 	=> isset( $saved_setting['bp_login_redirect_settings'][$tm->name]['login_component'] ) ? $saved_setting['bp_login_redirect_settings'][$tm->name]['login_component'] : '',
					'login_url' 		=> isset( $saved_setting['bp_login_redirect_settings'][$tm->name]['login_url'] ) ? $saved_setting['bp_login_redirect_settings'][$tm->name]['login_url'] : '',
				];

				$mem_type_setting['bp_logout_redirect_settings'][$tm->name] = [
					'logout_type'	=> isset( $saved_setting['bp_logout_redirect_settings'][$tm->name]['logout_type'] ) ? $saved_setting['bp_logout_redirect_settings'][$tm->name]['logout_type'] : '',					
					'logout_url'	=> isset( $saved_setting['bp_logout_redirect_settings'][$tm->name]['logout_url'] ) ? $saved_setting['bp_logout_redirect_settings'][$tm->name]['logout_url'] : '',
				];
			}		
		}

		$mem_type_setting['member_type_btn_value'] 	= isset( $saved_setting['member_type_btn_value'] ) ? $saved_setting['member_type_btn_value'] : '';
		$mem_type_setting['loginSequence'] 			= isset( $saved_setting['loginSequence'] ) ? $saved_setting['loginSequence'] : '';
		$mem_type_setting['logoutSequence'] 		= isset( $saved_setting['logoutSequence'] ) ? $saved_setting['logoutSequence'] : '';

		//check flag set or not on the existing setup
		$check_mem_type_data = get_option( 'flag_member_type_data' );

		// if flag isnot set then update the member type data on the new keys
		if ( ! ( $check_mem_type_data ) ) {
			update_option('bp_redirect_member_type_admin_settings', $mem_type_setting);
			update_option( 'flag_member_type_data', 1 );
		}
}
add_action('admin_init','bp_redirect_update_member_type_data_on_new_key');