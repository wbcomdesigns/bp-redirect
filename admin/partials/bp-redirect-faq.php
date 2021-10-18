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
?>
<div class="wbcom-tab-content" id="bpr-faq-section">
		<div class="bp-redirect-tab-header"><h3><?php esc_html_e( 'FAQ(s)', 'bp-redirect' ); ?></h3></div>
		
	<div class="bpr-row">
		<div class="bpr-ques">
			<?php esc_html_e( 'Is this plugin requires another plugin?', 'bp-redirect' ); ?>
		</div>
		<div class="bpr-ans">
			<?php esc_html_e( 'Yes, this plugin requires BuddyPress plugin.', 'bp-redirect' ); ?>
		</div>
	</div>
	<div class="bpr-row">
		<div class="bpr-ques">
			<?php esc_html_e( 'Where it redirects if no option selected or in the case of empty custom URL field?', 'bp-redirect' ); ?>
		</div>
		<div class="bpr-ans">
			<?php esc_html_e( 'In that case, plugin follows default redirection rule.', 'bp-redirect' ); ?>
		</div>
	</div>
	<div class="bpr-row">
		<div class="bpr-ques">
			<?php esc_html_e( 'Where it redirects, when we select "Member profile" in BuddyPress Component dropdown?', 'bp-redirect' ); ?>
		</div>
		<div class="bpr-ans">
			<?php esc_html_e( "It redirects to logged in member's profile page", 'bp-redirect' ); ?>
		</div>
	</div>
	<div class="bpr-row">
		<div class="bpr-ques">
			<?php esc_html_e( 'Where it redirects, when we select "Member activity" in BuddyPress Component dropdown?', 'bp-redirect' ); ?>
		</div>
		<div class="bpr-ans">
			<?php esc_html_e( "It redirects to logged in member's activity page", 'bp-redirect' ); ?>
		</div>
	</div>
	<div class="bpr-row">
		<div class="bpr-ques">
			<?php esc_html_e( 'Where it redirects, when we select "Activity" in BuddyPress Component dropdown?', 'bp-redirect' ); ?>
		</div>
		<div class="bpr-ans">
			<?php esc_html_e( 'It redirects to logged in site wide activity page', 'bp-redirect' ); ?>
		</div>
	</div>
	<div class="bpr-row">
		<div class="bpr-ques">
			<?php esc_html_e( 'Where do I ask for support?', 'bp-redirect' ); ?>
		</div>
		<div class="bpr-ans">
			<?php esc_html_e( 'Please visit', 'bp-redirect' ); ?> <a href="<?php echo esc_url( 'https://wbcomdesigns.com/contact/' ); ?>" title="<?php esc_attr( 'Wbcom Designs' ); ?>" target="_blank" ><?php esc_html_e( 'Wbcom Designs', 'bp-redirect' ); ?></a> <?php esc_html_e( 'for any query related to plugin and BuddyPress.', 'bp-redirect' ); ?>
		</div>
	</div>
</div>

