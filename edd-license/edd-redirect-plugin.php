<?php

/*
Plugin Name: Sample Plugin
Plugin URI: http://pippinsplugins.com/
Description: Illustrates how to include an updater in your plugin for EDD Software Licensing
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
Version: 1.0
License: GNU General Public License v2.0 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
* For further details please visit http://docs.easydigitaldownloads.com/article/383-automatic-upgrades-for-wordpress-plugins
 */

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'EDD_REDIRECT_STORE_URL', 'https://wbcomdesigns.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the download ID for the product in Easy Digital Downloads
define( 'EDD_REDIRECT_ITEM_ID', 80951 ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of the settings page for the license input to be displayed
define( 'EDD_REDIRECT_PLUGIN_LICENSE_PAGE', 'redirect-license' );

if( !class_exists( 'EDD_REDIRECT_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_REDIRECT_Plugin_Updater.php' );
}

define('EDD_REDIRECT_ITEM_NAME', 'BuddyPress Redirect');

function edd_redirect_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'edd_redirect_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_REDIRECT_Plugin_Updater( EDD_REDIRECT_STORE_URL, __FILE__,
		array(
			'version' => REDIRECT_PLUGIN_VERSION,                    // current version number
			'license' => $license_key,
			'item_name' => EDD_REDIRECT_ITEM_NAME,
			'item_id' => EDD_REDIRECT_ITEM_ID,       // ID of the product
			'author'  => 'Wbcom Designs', // author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'admin_init', 'edd_redirect_plugin_updater', 0 );


/************************************
* the code below is just a standard
* options page. Substitute with
* your own.
*************************************/

function edd_redirect_license_menu() {
	add_plugins_page( 'BP Redirect License', 'BP Redirect License', 'manage_options', EDD_REDIRECT_PLUGIN_LICENSE_PAGE, 'edd_redirect_license_page' );
}
add_action('admin_menu', 'edd_redirect_license_menu');

function edd_redirect_license_page() {
	$license = get_option( 'edd_redirect_license_key' );
	$status  = get_option( 'edd_redirect_license_status' );
	?>
	<div class="wrap">
		<h2><?php _e('BuddyPress Redirect License Options'); ?></h2>
		<form method="post" action="options.php">

			<?php settings_fields('edd_redirect_license'); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key'); ?>
						</th>
						<td>
							<input id="edd_redirect_license_key" name="edd_redirect_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="edd_redirect_license_key"><?php _e('Enter your license key for BuddyPress Redirect plugin.'); ?></label>
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active'); ?></span>
									<?php wp_nonce_field( 'edd_redirect_nonce', 'edd_redirect_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
								<?php } else {
									wp_nonce_field( 'edd_redirect_nonce', 'edd_redirect_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php submit_button(); ?>

		</form>
	<?php
}

function edd_redirect_register_option() {
	// creates our settings in the options table
	register_setting('edd_redirect_license', 'edd_redirect_license_key', 'edd_sanitize_license' );
}
add_action('admin_init', 'edd_redirect_register_option');

function edd_sanitize_license( $new ) {
	$old = get_option( 'edd_redirect_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'edd_redirect_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}



/************************************
* this illustrates how to activate
* a license key
*************************************/

function edd_redirect_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_activate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_redirect_nonce', 'edd_redirect_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'edd_redirect_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_REDIRECT_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_REDIRECT_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :
					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), EDD_REDIRECT_ITEM_NAME );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again.' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'plugins.php?page=' . EDD_REDIRECT_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'edd_redirect_license_status', $license_data->license );
		wp_redirect( admin_url( 'plugins.php?page=' . EDD_REDIRECT_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action('admin_init', 'edd_redirect_activate_license');


/***********************************************
* Illustrates how to deactivate a license key.
* This will decrease the site count
***********************************************/

function edd_redirect_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_redirect_nonce', 'edd_redirect_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'edd_redirect_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_REDIRECT_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_REDIRECT_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

			$base_url = admin_url( 'plugins.php?page=' . EDD_REDIRECT_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'edd_redirect_license_status' );
		}

		wp_redirect( admin_url( 'plugins.php?page=' . EDD_REDIRECT_PLUGIN_LICENSE_PAGE ) );
		exit();

	}
}
add_action('admin_init', 'edd_redirect_deactivate_license');


/************************************
* this illustrates how to check if
* a license key is still valid
* the updater does this for you,
* so this is only needed if you
* want to do something custom
*************************************/

function edd_redirect_check_license() {

	global $wp_version;

	$license = trim( get_option( 'edd_redirect_license_key' ) );

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( EDD_REDIRECT_ITEM_NAME ),
		'url'       => home_url()
	);

	// Call the custom API.
	$response = wp_remote_post( EDD_REDIRECT_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function edd_redirect_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch( $_GET['sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}
}
add_action( 'admin_notices', 'edd_redirect_admin_notices' );
