=== Wbcom Designs - BuddyPress Redirect ===
Contributors: wbcomdesigns
Donate link: https://wbcomdesigns.com/donate/
Tags: login redirect, buddypress redirect
Requires at least: 3.0.1
Tested up to: 6.5.2
Stable tag: 1.9.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This plugin serves the purpose of redirecting to different locations according to the user role. .

If you need additional help you can contact us for [Custom Development](https://wbcomdesigns.com/contact/).

== Installation ==

1. Upload the entire "bp-redirect" folder to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= How can we change redirection settings using this plugin ? =

After installation of BP Redirect plugin, a new menu will be added. This menu links to BP Redirect settings page & includes all the redirection settings in admin panel.

= Is this plugin work only with BuddyPress plugin ? =

No, BP Redirect plugin is not required BuddyPress plugin to be active.

== Screenshots ==
1. The screenshot shows the admin settings to change redirect on login and logout for different roles and corresponds to screenshot-1.(png|jpg|jpeg|gif).
2. The screenshot shows the admin settings to change roles sequence by drag & drop and corresponds to screenshot-2.(png|jpg|jpeg|gif).

== Changelog ==
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

= 1.8.2 =
* Fix: (#63) Fatal error when logout

= 1.8.1 =
* Fix: (#59) wp plugin active redirect issue fix
* Fix: (#59) Logout redirection with member
* Fix: (#59) Global login redirection is not working for the BP Component
* Fix: (#50)Fixed fatal error on user role logout
* Fix: (#55) PHP fixes
* Fix: (#58) Issue in member type redirection
* Fix: (#57) Solve logout redirect fatal error
* Fix: (#57) Can't save user role settings in the absence of BuddyPress
* Fix: (#47) Buddypress activity selection issue
* Fix: (#47) Multiple members or multiple role fixes
* Fix: (#47) wp redirect custom field for all users and specific user
* Fix: (#54) Accordion UI fixes
* Fix: (#52) Added global setting nav menu icon
* Fix: (#47) custom URL for login, logout redirection
* Fix: (#45) BuddyPress Dependency
* Fix: Plugin redirect issue when multiple plugins activate at the same time

= 1.7.2 =
* Fix: backend UI issue fixed
* Fix: Fixed buddyboss admin notice issue

= 1.7.1 =
* Fix: Updated admin wrapper UI

= 1.7.0 =
* Fix: Updated Admin UI
* Fix: (#37) Fixed bp member type link redirection with buddyboss

= 1.6.0 =
* Fix: Fixed plugin installation issue
* Fix: Fix phpcs error

= 1.5.0 =
* Fix: general tab issue
* Fix: setting and redirect url when plugin activate
* Fix: member type not exist issue in admin option
* Fix: dashboard FAQ section
* Fix: dashboard UI Section
* Fix: #22 - Fixed member type redirect issue
* Fix: dropdown option issue when click on radio button
* Fix: Save enable button value
* Fix: Add enable button css and js

= 1.3.0 =
* Fix: (#8) Fixed all BuddyPress Components are not showing in the dropdown
* Fix: (#9) Fixced Log In and Logout Redirect Settings are not Working
* Fix: (#7) Fixed PHPCS issues

= 1.2.0 =
* Fix: Added Licence keys for future auto updates.
* Fix: Login redirect with latest BuddyPress and BuddyBoss.

= 1.1.0 =
* Fix: BuddyPress plugin admin notice

= 1.0.0 =
* Initial release.

== Upgrade Notice ==
= 1.0.0 =
This version is the initial version of the plugin with basic review adding functionality.
