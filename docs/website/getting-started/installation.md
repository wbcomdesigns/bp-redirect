# Installation and Requirements

BuddyPress Redirect (plugin name in WordPress: "Wbcom Designs - Login Logout Redirect") controls where users are sent after they log in and log out. It works on any WordPress site with no third-party plugin required, and unlocks extra destinations when BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, or PeepSo are active.

## Requirements

| Requirement | Value |
|---|---|
| WordPress | 5.6 or higher |
| PHP | 7.4 or higher |
| Tested up to | WordPress 6.9 |
| Dependencies | None (standalone). BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, and PeepSo are optional and auto-detected. |

Values are taken from the plugin header and `readme.txt` (version 2.1.0).

## Install from the admin dashboard

1. Download the plugin ZIP.
2. In WordPress go to Plugins > Add New > Upload Plugin.
3. Choose the ZIP file and select Install Now.
4. Select Activate.

## Install manually over FTP

1. Unzip the plugin.
2. Upload the `bp-redirect` folder to `/wp-content/plugins/`.
3. Go to Plugins in the WordPress admin and activate "Wbcom Designs - Login Logout Redirect".

## After activation

On activation the plugin:

- Creates default settings if none exist. The Global redirect starts disabled; the User Role redirect starts enabled but with no roles configured, so nothing is redirected until you add a rule.
- Redirects you once to its settings screen at WB Plugins > Redirect.

You can reopen the settings anytime from WB Plugins > Redirect, or from the Settings link on the Plugins list row.
