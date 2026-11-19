# Installation and Requirements

BuddyPress Redirect (plugin name in WordPress: "WB Login Logout Redirect") controls where users are sent after they log in and log out. It works on any WordPress site with no third-party plugin required, and unlocks extra destinations when BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, or PeepSo are active.

## Requirements

| Requirement | Value |
|---|---|
| WordPress | 6.5 or higher |
| PHP | 8.1 or higher |
| Tested up to | WordPress 7.1 |
| Dependencies | None (standalone). BuddyPress, BuddyBoss, WooCommerce, bbPress, Dokan, LearnDash, and PeepSo are optional and auto-detected. |

WordPress and PHP minimums are the plugin's own; BuddyPress is not required. No license key is needed.

## Install from the admin dashboard

1. Download the plugin ZIP.
2. In WordPress go to Plugins > Add New > Upload Plugin.
3. Choose the ZIP file and select Install Now.
4. Select Activate.

## Install manually over FTP

1. Unzip the plugin.
2. Upload the `bp-redirect` folder to `/wp-content/plugins/`.
3. Go to Plugins in the WordPress admin and activate "WB Login Logout Redirect".

## After activation

On activation the plugin:

- Creates default settings if none exist. The Global redirect starts disabled; the User Role redirect starts enabled but with no roles configured, so nothing is redirected until you add a rule.
- Redirects you once to its settings screen at WB Plugins > Login Redirect.

You can reopen the settings anytime from WB Plugins > Login Redirect, or from the Settings link on the Plugins list row.

## Automatic updates

The plugin updates itself through the Wbcom Designs update service. There is no license key to enter: the License tab shows **Automatic updates: Enabled** when this is working. If the update files (the `libs` folder) are missing from the plugin folder, administrators see a notice and the License tab shows **Off**. Reinstall the plugin from your Wbcom Designs account to turn updates back on.

Versions before 2.3.0 could not find the built-in key, so a site on 2.2.x or earlier may never have shown an update notice. If your site is on one of those versions, download 2.3.0 from your Wbcom Designs account and install it over the old copy. From 2.3.0 on, updates appear in Plugins as usual. See [What's new in 2.3.0](../whats-new/whats-new-in-2-3-0.md).

## Removing the plugin

Deleting the plugin does not remove its settings. The options `wbcom_redirect_global`, `wbcom_redirect_roles`, `wbcom_redirect_migrated` and one `wbcom_redirect_` option per member type integration stay in the database, so reinstalling restores your rules. The plugin stores no data about individual users; it holds only these rule options.
