<?php
/**
 * Provide an admin area view for the vendor's settings page.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    BuddyPress Redirect
 * @subpackage bp-redirect/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
		$spinner_src = includes_url() . 'images/spinner.gif';

		$saved_setting = get_option( 'bp_redirect_admin_settings_global' );
		$bp_pages      = get_pages();
		$bp_pages_ids  = array_values( $bp_pages );

		$login_sequence = $this->get_editable_roles();

if ( ! empty( $saved_setting ) ) {
	if ( array_key_exists( 'loginSequence', $saved_setting ) ) {
		$seq = explode( ',', $saved_setting['loginSequence'] );

		foreach ( $seq as $key => $val ) {
				$val_arr     = $val;
				$seq[ $key ] = $val_arr;
		}
	}
}

$logout_sequence = $this->get_editable_roles();
if ( ! empty( $saved_setting ) ) {
	if ( array_key_exists( 'logoutSequence', $saved_setting ) ) {
		$logoutseq = explode( ',', $saved_setting['logoutSequence'] );
		foreach ( $logoutseq as $key => $val ) {
				$val_arr           = $val;
				$logoutseq[ $key ] = $val_arr;

		}
	}
}

?>

<div class="wbcom-tab-content">
	<div class="wbcom-wrapper-admin">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e( 'Global Redirection Setting', 'bp-redirect' ); ?></h3>
		</div><!-- .wbcom-welcome-head -->
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">			
			<div id="bpredirect-settings_updated" class="updated settings-error notice is-dismissible">
				<p><strong><?php esc_html_e( 'Settings saved.', 'bp-redirect' ); ?></strong></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'bp-redirect' ); ?></span></button>
			</div>
			<form class="user-role-setting" method="post">
				<div class="enable_disable_btn wbcom-settings-section-wrap">
					<label for="bp-redirect" class="enable_disable_setting">
						<?php esc_html_e( 'Enable Global Redirection for all the users', 'bp-redirect' ); ?>
					</label>
					<input type="checkbox" class="wppd-ui-toggle" id="bp_role_enable_disable" name="role_btn_value" value="yes"<?php ( isset( $saved_setting['role_btn_value'] ) ) ? checked( $saved_setting['role_btn_value'], 'yes' ) : ''; ?>>
					<input type="hidden" name="bp_enable_disable_role_checkbox" value="<?php echo ( isset( $saved_setting['role_btn_value'] ) ) ? esc_attr( $saved_setting['role_btn_value'] ) : 'no'; ?>">
				</div>
			</form>

			<div class="bpr-row bpr-row-wrapper wbcom-settings-section-wrap" 
			<?php
			if ( ! isset( $saved_setting['role_btn_value'] ) || 'no' === $saved_setting['role_btn_value'] ) {
				?>
			style="display:none" <?php } ?>>
				<div class="row">
					<div class="bpr-col-12">
					<!-- login Settings -->
					<h2><?php esc_html_e( 'Login Redirect Settings', 'bp-redirect' ); ?></h2>
					<?php $this->bp_redirect_plugin_global_login_settings( $login_sequence, $bp_pages_ids, $saved_setting ); ?>
				</div>
					</div>
			</div>
			<div class="bpr-row bpr-row-wrapper wbcom-settings-section-wrap" 
			<?php
			if ( ! isset( $saved_setting['role_btn_value'] ) || 'no' === $saved_setting['role_btn_value'] ) {
				?>
			style="display:none" <?php } ?>>
				<div class="row">
				<div class="bpr-col-12">
					<!-- Logout Settings -->
					<h2><?php esc_html_e( 'Logout Redirect Settings', 'bp-redirect' ); ?></h2>
				<?php $this->bp_redirect_plugin_global_logout_settings( $logout_sequence, $bp_pages_ids, $saved_setting ); ?>
					</div>
					</div>
			</div>
			<p>
				<button id="bp-redirect-globel-settings-submit" class="button button-primary" name="bp-redirect-globel-settings-submit"><?php esc_html_e( 'Save Settings', 'bp-redirect' ); ?></button><img src="<?php echo esc_url( $spinner_src, 'bp-redirect' ); ?>" class="bp-redirect-settings-spinner" />
			</p>
			<div id="bpredirect-settings_updated-footer" class="" style="display:none">
				<p><strong><?php esc_html_e( 'Settings saved.', 'bp-redirect' ); ?></strong></p>		
			</div>
		</div>
	</div>
</div>






