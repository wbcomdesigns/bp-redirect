<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wbcomdesigns.com/contact/
 * @since      1.0.0
 *
 * @package    BP_Redirect
 * @subpackage BP_Redirect/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    BP_Redirect
 * @subpackage BP_Redirect/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class BP_Redirect_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$bp_redirect_default_settings = array(
			'role_btn_value' => 'yes'
		);

		$bp_redirect_member_type_default_setting = array(
			'member_type_btn_value' => 'yes'
		);

		update_option( 'bp_redirect_admin_settings', $bp_redirect_default_settings );
		update_option( 'bp_redirect_admin_settings_global', $bp_redirect_default_settings );
		update_option( 'bp_redirect_member_type_admin_settings', $bp_redirect_member_type_default_setting );

	}

}
