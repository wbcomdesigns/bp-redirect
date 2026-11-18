<?php
/**
 * WB Plugins hub — landing dashboard at ?page=wbcomplugins.
 *
 * Lists every Wbcom plugin that has registered a submenu under the
 * shared wbcomplugins parent. Peer plugins appear automatically once
 * their admin page is in the WordPress menu table. Legacy wbcom-wrapper
 * helper pages are filtered out so they don't read as duplicate tiles.
 * See references/wbcom-wrapper-migration.md Part 15.
 *
 * @package Wbcom_Redirect
 * @since   2.3.0
 */

defined( 'ABSPATH' ) || exit;

$wbcom_redirect_submenu_entries = isset( $GLOBALS['submenu']['wbcomplugins'] ) && is_array( $GLOBALS['submenu']['wbcomplugins'] )
	? $GLOBALS['submenu']['wbcomplugins']
	: array();

// Slugs of the legacy wbcom-wrapper boilerplate helper pages. Filterable
// so Pro modules or future wrapper slugs can be added.
$wbcom_redirect_wrapper_helper_slugs = apply_filters(
	'wbcom_hub_wrapper_helper_slugs',
	array(
		'wbcom-plugins-page',
		'wbcom-themes-page',
		'wbcom-support-page',
		'wbcom-license-page',
	)
);

$wbcom_redirect_plugins = array();
foreach ( $wbcom_redirect_submenu_entries as $wbcom_redirect_entry ) {
	$wbcom_redirect_slug = isset( $wbcom_redirect_entry[2] ) ? (string) $wbcom_redirect_entry[2] : '';
	if ( '' === $wbcom_redirect_slug || 'wbcomplugins' === $wbcom_redirect_slug ) {
		continue;
	}
	if ( in_array( $wbcom_redirect_slug, $wbcom_redirect_wrapper_helper_slugs, true ) ) {
		continue;
	}
	$wbcom_redirect_plugins[] = array(
		'slug'       => $wbcom_redirect_slug,
		'menu_title' => isset( $wbcom_redirect_entry[0] ) ? wp_strip_all_tags( (string) $wbcom_redirect_entry[0] ) : $wbcom_redirect_slug,
		'page_title' => isset( $wbcom_redirect_entry[3] ) ? wp_strip_all_tags( (string) $wbcom_redirect_entry[3] ) : '',
		'url'        => admin_url( 'admin.php?page=' . rawurlencode( $wbcom_redirect_slug ) ),
	);
}

$wbcom_redirect_plugin_count = count( $wbcom_redirect_plugins );
?>
<div class="wrap wbredir-admin">
	<header class="wbredir-page-header">
		<div class="wbredir-page-header__title">
			<span class="dashicons dashicons-lightbulb" aria-hidden="true"></span>
			<div>
				<h1><?php esc_html_e( 'WB Plugins', 'bp-redirect' ); ?></h1>
				<p class="wbredir-page-header__subtitle">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: active Wbcom plugin count */
							_n(
								'%d Wbcom plugin active on this site.',
								'%d Wbcom plugins active on this site.',
								$wbcom_redirect_plugin_count,
								'bp-redirect'
							),
							$wbcom_redirect_plugin_count
						)
					);
					?>
				</p>
			</div>
		</div>
	</header>

	<?php if ( 0 === $wbcom_redirect_plugin_count ) : ?>
		<div class="wbredir-empty-state">
			<span class="wbredir-empty-state__icon" aria-hidden="true">
				<span class="dashicons dashicons-lightbulb"></span>
			</span>
			<p class="wbredir-empty-state__title"><?php esc_html_e( 'No Wbcom plugins attached to this hub yet', 'bp-redirect' ); ?></p>
			<p class="wbredir-empty-state__desc">
				<?php esc_html_e( 'Activate one or more Wbcom plugins and they will appear here automatically.', 'bp-redirect' ); ?>
			</p>
		</div>
	<?php else : ?>
		<div class="wbredir-hub-grid">
			<?php foreach ( $wbcom_redirect_plugins as $wbcom_redirect_p ) : ?>
				<a href="<?php echo esc_url( $wbcom_redirect_p['url'] ); ?>" class="wbredir-hub-card">
					<span class="wbredir-hub-card__icon" aria-hidden="true">
						<span class="dashicons dashicons-admin-plugins"></span>
					</span>
					<span class="wbredir-hub-card__title"><?php echo esc_html( $wbcom_redirect_p['menu_title'] ); ?></span>
					<?php if ( ! empty( $wbcom_redirect_p['page_title'] ) && $wbcom_redirect_p['page_title'] !== $wbcom_redirect_p['menu_title'] ) : ?>
						<span class="wbredir-hub-card__subtitle"><?php echo esc_html( $wbcom_redirect_p['page_title'] ); ?></span>
					<?php endif; ?>
					<span class="wbredir-hub-card__cta">
						<?php esc_html_e( 'Open settings', 'bp-redirect' ); ?>
						<span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="wbredir-card" style="margin-top: 20px;">
		<div class="wbredir-card__head">
			<p class="wbredir-card__title"><?php esc_html_e( 'About WB Plugins', 'bp-redirect' ); ?></p>
		</div>
		<div class="wbredir-card__body">
			<p style="margin: 0 0 8px;">
				<?php esc_html_e( 'This hub is the single entry point for every Wbcom Designs plugin installed on your site. Each plugin lives on its own page under this menu and keeps its own settings, license, and data.', 'bp-redirect' ); ?>
			</p>
			<p style="margin: 0;">
				<a href="https://wbcomdesigns.com/" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Visit wbcomdesigns.com for more plugins and themes →', 'bp-redirect' ); ?>
				</a>
			</p>
		</div>
	</div>
</div>
