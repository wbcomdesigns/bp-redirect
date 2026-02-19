=== Wbcom Designs - Login Logout Redirect ===
Contributors: wbcomdesigns
Donate link: https://wbcomdesigns.com/donate/
Tags: login redirect, logout redirect, role redirect, woocommerce redirect, buddypress redirect
Requires at least: 5.6
Tested up to: 6.9
Stable tag: 2.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Redirect users after login and logout based on roles. Works standalone with optional BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, and PeepSo support.

== Description ==

**Login Logout Redirect** is a powerful, free WordPress plugin that lets you control where users are redirected after login and logout based on their roles.

The plugin works **completely standalone** — no third-party plugins required. When BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, or PeepSo are active, additional redirect destinations automatically become available.

= Features =

* **Global Redirect** — Set a default login/logout redirect for all users
* **Role-Based Redirect** — Configure separate redirects per user role (Administrator, Editor, Subscriber, etc.)
* **Page or Custom URL** — Redirect to any WordPress page or enter a custom URL
* **Integration Destinations** — Redirect to BuddyPress profiles, WooCommerce My Account, bbPress forums, and more
* **Priority Chain** — Role > Integration Group Types > Global > WordPress Default
* **Auto-Detection** — Integration options appear only when their parent plugin is active
* **BuddyPress Member Types** — Configure redirects per member type (when BuddyPress is active)
* **Clean Admin UI** — Unified settings interface with accordion-style role management

= Supported Integrations =

| Integration | Destinations |
|------------|-------------|
| BuddyPress | Member Profile, Member Activity, Groups Directory + Member Types |
| WooCommerce | My Account, Shop, Checkout, Orders |
| bbPress | Forums, User Profile |
| Dokan | Vendor Dashboard, Store Page |
| LearnDash | Student Dashboard, Courses |
| PeepSo | Profile, Activity Stream |

= How It Works =

1. **Install & Activate** — Plugin works immediately with WordPress defaults
2. **Configure Global Redirect** — Set where all users go after login/logout
3. **Configure Role Redirects** — Override globals per role (e.g., send Subscribers to their profile, Vendors to their dashboard)
4. **Integration Destinations** — When BuddyPress/WooCommerce/etc. are active, their pages appear as destination options

If you need additional help you can contact us for [Custom Development](https://wbcomdesigns.com/contact/).

== Installation ==

1. Upload the "bp-redirect" folder to `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to WB Plugins > Redirect to configure settings.

== Frequently Asked Questions ==

= Does this plugin require BuddyPress? =

No. This plugin works standalone on any WordPress site. BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, and PeepSo support is automatically enabled when those plugins are active.

= What is the redirect priority order? =

User Role > Integration Group Types (e.g. BuddyPress member types) > Global > WordPress Default.

= What happens if no redirect is configured? =

WordPress default behavior applies: admins go to the dashboard, other users go to the home page.

= Can I redirect WooCommerce customers to My Account after login? =

Yes! When WooCommerce is active, "My Account", "Shop", "Checkout", and "Orders" appear as integration destination options.

= Will my v2.0.0 settings be preserved? =

Yes. On first load after updating to v2.1.0, your existing settings are automatically migrated to the new format.

== Screenshots ==

1. Global Redirection Settings — configure login/logout redirects for all users.
2. User Role Settings — accordion-style per-role login/logout redirect configuration.

== Changelog ==

= 2.1.0 =
* Major: Rebuilt as standalone WordPress plugin — no longer requires BuddyPress.
* New: Integration framework with auto-detection for BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, PeepSo.
* New: WooCommerce destinations (My Account, Shop, Checkout, Orders).
* New: bbPress destinations (Forums, User Profile).
* New: Dokan destinations (Vendor Dashboard, Store Page).
* New: LearnDash destinations (Student Dashboard, Courses).
* New: PeepSo destinations (Profile, Activity Stream).
* New: Priority chain resolver: Role > Group Types > Global > Default.
* New: Unified redirect settings form (Page / Custom URL / Integration Destination).
* New: Dynamic admin tabs from active integrations (e.g. BuddyPress Member Types tab).
* New: Clean admin JS with modern AJAX save.
* New: Auto-migration from v2.0.0 settings.
* Enhancement: Cleaner settings structure using uniform config shape.
* Enhancement: Proper wp_send_json_success/error responses for AJAX.

= 2.0.0 =
* Fix: Fixed security issue with input sanitization and validation.
* Fix: Added security check and updated readme.
* Fix: Resolved custom URL redirection issue.
* Fix: Added notice dismiss functionality.
* Fix: Resolved default selected options related issue.
* Fix: Removed activation notice when activation failed.
* Fix: Fixed admin CSS not loading when other Wbcom plugins are active.
* Fix: Fixed logout settings not displaying saved values in admin.
* Fix: Fixed member type settings read/write mismatch on multisite.
* Fix: Fixed member type tab reading wrong option key in fallback.
* Enhancement: Optimized code in admin and public classes.
* Enhancement: Resolved PHPCS errors and managed RTL fixes.
* Enhancement: Implemented conditions for enqueuing scripts.
* Enhancement: Added minified and RTL support.
* Enhancement: Modified function prefixes to consistent naming.
* Enhancement: Updated plugin updater.
* Fix: Resolved logout redirection issue with Youzify.
* Enhancement: Default settings are now marked as enabled.
* Enhancement: Removed unwanted files and code from wbcom folder.

= 1.9.0 =
* Fix: Resolved plugin activation issue and various warnings.
* Fix: Updated admin section code and improved the handling of profile and activity URLs.
* Fix: Addressed fatal error and issues with global redirect, console error, and "none" option for login/logout settings.
* Update: Removed unnecessary option update/get code, optimized login/logout handling, and improved redirection logic.
* Fix: Added functionality to save member type options on existing setups and ensured proper handling of login component and URL fields.
* Enhancement: Improved default values for role-based settings, array checks, and fallback mechanisms.
* Fix: Deprecated warning fixes and refined language clarity in the BP Redirect plugin.
* Enhancement: Removed unused code and improved consistency in the overall plugin structure.

= 1.8.3 =
* Fix: Console error
* Updated: Banner link
* Fix: BP v12 fixes
* Fix: phpcs fixes

= 1.0.0 =
* Initial release.

== Upgrade Notice ==
= 2.1.0 =
Major update: Plugin now works standalone without BuddyPress. Adds WooCommerce, bbPress, Dokan, LearnDash, and PeepSo integration support. Settings auto-migrate from v2.0.0.
