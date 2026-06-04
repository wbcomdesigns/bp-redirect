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
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

$submenu_entries = isset( $GLOBALS['submenu']['wbcomplugins'] ) && is_array( $GLOBALS['submenu']['wbcomplugins'] )
	? $GLOBALS['submenu']['wbcomplugins']
	: array();

// Slugs of the legacy wbcom-wrapper boilerplate helper pages. Filterable
// so Pro modules or future wrapper slugs can be added.
$wrapper_helper_slugs = apply_filters(
	'wbcom_hub_wrapper_helper_slugs',
	array(
		'wbcom-plugins-page',
		'wbcom-themes-page',
		'wbcom-support-page',
		'wbcom-license-page',
	)
);

$wbredir_plugins = array();
foreach ( $submenu_entries as $entry ) {
	$slug = isset( $entry[2] ) ? (string) $entry[2] : '';
	if ( '' === $slug || 'wbcomplugins' === $slug ) {
		continue;
	}
	if ( in_array( $slug, $wrapper_helper_slugs, true ) ) {
		continue;
	}
	$wbredir_plugins[] = array(
		'slug'       => $slug,
		'menu_title' => isset( $entry[0] ) ? wp_strip_all_tags( (string) $entry[0] ) : $slug,
		'page_title' => isset( $entry[3] ) ? wp_strip_all_tags( (string) $entry[3] ) : '',
		'url'        => admin_url( 'admin.php?page=' . rawurlencode( $slug ) ),
	);
}

$plugin_count = count( $wbredir_plugins );
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
								$plugin_count,
								'bp-redirect'
							),
							$plugin_count
						)
					);
					?>
				</p>
			</div>
		</div>
	</header>

	<?php if ( 0 === $plugin_count ) : ?>
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
			<?php foreach ( $wbredir_plugins as $p ) : ?>
				<a href="<?php echo esc_url( $p['url'] ); ?>" class="wbredir-hub-card">
					<span class="wbredir-hub-card__icon" aria-hidden="true">
						<span class="dashicons dashicons-admin-plugins"></span>
					</span>
					<span class="wbredir-hub-card__title"><?php echo esc_html( $p['menu_title'] ); ?></span>
					<?php if ( ! empty( $p['page_title'] ) && $p['page_title'] !== $p['menu_title'] ) : ?>
						<span class="wbredir-hub-card__subtitle"><?php echo esc_html( $p['page_title'] ); ?></span>
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
