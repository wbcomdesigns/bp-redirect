<?php
/**
 * Display faq(s)
 *
 * @since  1.0.0
 * @author Wbcom Designs
 * @access public
 *
 * @package BP_Redirect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wbcom-tab-content" id="bpr-faq-section">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e( 'FAQ(s) ', 'bp-redirect' ); ?></h3>
			<input type="hidden" class="wb-ads-tab-active" value="support"/>
		</div>
	<div class="bpmmd-xprofile-admin-settings-block">
		<div id="faq_bpmmd_accordion" class="wb-ads-table">
				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion">
							<?php esc_html_e( 'Is this plugin requires another plugin?', 'bp-redirect' ); ?>
						</button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'Yes, this plugin requires BuddyPress plugin.', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>
				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion">
							<?php esc_html_e( 'Where it redirects if no option selected or in the case of empty custom URL field?', 'bp-redirect' ); ?>
						</button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'In that case, plugin follows default redirection rule.', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>
				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion">
							<?php esc_html_e( 'Where it redirects, when we select "Member profile" in BuddyPress Component dropdown?', 'bp-redirect' ); ?>
						</button>
						<div class="bpr-panel">
							<p><?php esc_html_e( "It redirects to logged in member's profile page", 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>
				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion">
							<?php esc_html_e( 'Where it redirects, when we select "Member activity" in BuddyPress Component dropdown?', 'bp-redirect' ); ?>
						</button>
						<div class="bpr-panel">
							<p><?php esc_html_e( "It redirects to logged in member's activity page", 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>
				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion">
							<?php esc_html_e( 'Where it redirects, when we select "Activity" in BuddyPress Component dropdown?', 'bp-redirect' ); ?>
						</button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'It redirects to logged in site wide activity page', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>
				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion">
							<?php esc_html_e( 'Can I use user role and member type redirection both?', 'bp-redirect' ); ?>
						</button>
						<div class="bpr-panel">
							<p><?php esc_html_e( "There will be no conflict, but It's advised to use either User role redirection or Member type redirection at a time.", 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>
				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion">
							<?php esc_html_e( 'Where do I ask for support?', 'bp-redirect' ); ?>
						</button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'Please visit', 'bp-redirect' ); ?> <a href="<?php echo esc_url( 'https://wbcomdesigns.com/contact/' ); ?>" title="<?php esc_attr( 'Wbcom Designs' ); ?>" target="_blank" ><?php esc_html_e( 'Wbcom Designs', 'bp-redirect' ); ?></a> <?php esc_html_e( 'for any query related to plugin and BuddyPress.', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> 
