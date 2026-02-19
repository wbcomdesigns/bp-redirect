<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wbcom-tab-content" id="bpr-faq-section">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e( 'Frequently Asked Questions', 'bp-redirect' ); ?></h3>
		</div>
		<div class="bpmmd-xprofile-admin-settings-block">
			<div id="faq_bpmmd_accordion" class="wb-ads-table">

				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion"><?php esc_html_e( 'Does this plugin require BuddyPress?', 'bp-redirect' ); ?></button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'No. This plugin works standalone on any WordPress site. BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, and PeepSo support is automatically enabled when those plugins are active.', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>

				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion"><?php esc_html_e( 'What happens if no redirect is configured?', 'bp-redirect' ); ?></button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'WordPress default behavior applies: admins go to the dashboard, other users go to the profile or home page.', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>

				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion"><?php esc_html_e( 'What is the redirect priority order?', 'bp-redirect' ); ?></button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'User Role redirect takes highest priority, followed by Integration Group Types (e.g. BuddyPress member types), then Global redirect, and finally WordPress default.', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>

				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion"><?php esc_html_e( 'Can I use role and BuddyPress member type redirects together?', 'bp-redirect' ); ?></button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'Yes! Role-based redirects take priority. If no role redirect is configured for a user, the plugin will check their BuddyPress member type, then fall back to global settings.', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>

				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion"><?php esc_html_e( 'Which integrations are supported?', 'bp-redirect' ); ?></button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'BuddyPress (Profile, Activity, Groups + Member Types), WooCommerce (My Account, Shop, Checkout, Orders), bbPress (Forums, User Profile), Dokan (Vendor Dashboard, Store Page), LearnDash (Dashboard, Courses), and PeepSo (Profile, Activity).', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>

				<div class="bpmmd-xprofile-admin-row">
					<div class="bpmmd-xprofile-admin-col-12">
						<button class="bpr-accordion"><?php esc_html_e( 'Where do I ask for support?', 'bp-redirect' ); ?></button>
						<div class="bpr-panel">
							<p><?php esc_html_e( 'Please visit', 'bp-redirect' ); ?> <a href="https://wbcomdesigns.com/contact/" target="_blank"><?php esc_html_e( 'Wbcom Designs', 'bp-redirect' ); ?></a> <?php esc_html_e( 'for any query related to the plugin.', 'bp-redirect' ); ?></p>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
