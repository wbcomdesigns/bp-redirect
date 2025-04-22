<?php
/**
 * Provide an admin area view for the vendor's settings page.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    BP_Redirect
 * @subpackage BP_Redirect/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	$terms = get_terms(
		array(
			'taxonomy'   => 'bp_member_type',
			'hide_empty' => false,
		)
	);

	$member_types = array();
	foreach ( $terms as $key => $tm ) {
		$temp                      = array();
		$temp['name']              = ucfirst( $tm->name );
		$temp['capabilities']      = array();
		$member_types[ $tm->slug ] = $temp;

	}

	$spinner_src = includes_url() . 'images/spinner.gif';
	if ( class_exists( 'Buddypress' ) ) {
		$saved_setting = bp_get_option( 'bp_redirect_member_type_admin_settings' );
		$bp_pages      = bp_get_option( 'bp-pages' );
	} else {
		$saved_setting = get_option( 'bp_redirect_admin_settings_global' );
		$bp_pages      = get_pages();
	}
	$bp_pages_ids   = array_values( $bp_pages );
	$login_sequence = $member_types;

	if ( ! empty( $saved_setting ) ) {
		if ( array_key_exists( 'loginSequence', $saved_setting ) ) {
			$seq = explode( ',', $saved_setting['loginSequence'] );
			foreach ( $seq as $key => $val ) {
				$val_arr     = $val;
				$seq[ $key ] = $val_arr;
			}
		}
	}

	$logout_sequence = $member_types;
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
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e( 'Redirect For Member Type', 'bp-redirect' ); ?></h3>
		</div><!-- .wbcom-welcome-head -->
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
			<div id="bpredirect-settings_updated" class="updated settings-error notice is-dismissible">
				<p><strong><?php esc_html_e( 'Settings saved.', 'bp-redirect' ); ?></strong></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'bp-redirect' ); ?></span></button>
			</div>	
			<?php
			if ( ! empty( $login_sequence ) ) {
				?>
			<form method="post">
				<div class="enable_disable_btn wbcom-settings-section-wrap">
					<label for="bp-redirect" class="enable_disable_setting">
						<?php esc_html_e( 'Enable Setting For Buddypress Member Type', 'bp-redirect' ); ?>
					</label>
					<input type="checkbox" class="wppd-ui-toggle" id="bp_red_enable_disable" name="member_type_btn_value" value="yes"<?php ( isset( $saved_setting['member_type_btn_value'] ) ) ? checked( $saved_setting['member_type_btn_value'], 'yes' ) : ''; ?>>
					<input type="hidden" name="bp_enable_disable_member_checkbox" value="<?php echo ( isset( $saved_setting['member_type_btn_value'] ) ) ? esc_attr( $saved_setting['member_type_btn_value'] ) : 'no'; ?>">
				</div>
			</form>
			<div class="bpr-row wbcom-settings-section-wrap" 
						<?php
						if ( ! isset( $saved_setting['member_type_btn_value'] ) || 'no' === $saved_setting['member_type_btn_value'] ) {
							?>
		style="display:none" <?php } ?>>
				<div class="row">
					<div class="bpr-col-12">
					<!-- login Settings -->
					<h2><?php esc_html_e( 'Buddypress Login Redirect Settings', 'bp-redirect' ); ?></h2>				
						<?php $this->bp_redirect_plugin_login_settings( $login_sequence, $bp_pages_ids, $saved_setting ); ?>
					</div>
				</div>
			</div>
			<div class="bpr-row wbcom-settings-section-wrap" 
						<?php
						if ( ! isset( $saved_setting['member_type_btn_value'] ) || 'no' === $saved_setting['member_type_btn_value'] ) {
							?>
		style="display:none" <?php } ?>>
				<div class="row">
			<div class="bpr-col-12">
					<!-- Logout Settings -->
					<h2><?php esc_html_e( 'Buddypress Logout Redirect Settings', 'bp-redirect' ); ?></h2>
						<?php $this->bp_redirect_plugin_logout_settings( $login_sequence, $bp_pages_ids, $saved_setting ); ?>
				</div>
				</div>
			</div>
			<p>
				<button id="bp-redirect-settings-submit" class="button button-primary" data-attr="member-type" name="bp-redirect-settings-submit"><?php esc_html_e( 'Save Settings', 'bp-redirect' ); ?></button><img src="<?php echo esc_url( $spinner_src, 'bp-redirect' ); ?>" class="bp-redirect-settings-spinner" />
			</p>
			<div id="bpredirect-settings_updated-footer" class="" style="display:none">
				<p><strong><?php esc_html_e( 'Settings saved.', 'bp-redirect' ); ?></strong></p>		
			</div>
			<?php } else { ?>
			<h4>
					<?php esc_html_e( 'Buddypress Member Type Not Exist, Create Member Type Click On The Link -: ', 'bp-redirect' ); ?>
					<?php if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) { ?>				
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=bp-member-type' ) ); ?>" target="_blank"><?php esc_html_e( 'Click Here', 'bp-redirect' ); ?></a>
				<?php } elseif ( class_exists( 'BuddyPress' ) ) { ?>
					<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=bp_member_type' ) ); ?>" target="_blank"><?php esc_html_e( 'Click Here', 'bp-redirect' ); ?></a>
				<?php } ?>
			</h4>
			<?php } ?>
		</div>
	</div>
</div>
