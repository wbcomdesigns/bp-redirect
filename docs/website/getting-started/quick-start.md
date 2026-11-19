# Quick Start

This walkthrough sets up a redirect in under a minute. All settings live under WB Plugins > Login Redirect in the WordPress admin.

## Send everyone to one page after login

1. Go to WB Plugins > Login Redirect and open the Global Redirection tab.
2. Turn on Enable Global Redirection.
3. Under Login Redirect, choose Page and select a published page, or choose Custom URL and enter a full URL.
4. Select Save Settings.

The next time any user logs in, they are sent to that destination unless a higher-priority rule (a role rule, or a BuddyPress member type rule) applies to them.

## Send a specific role somewhere else

1. Open the User Roles tab.
2. Make sure Enable User Role Redirection is on.
3. Expand the role you want (for example Subscriber).
4. Set its Login Redirect and Logout Redirect to a Page, a Custom URL, or an Integration Destination.
5. Select Save Settings.

Role rules override the Global rule for users in that role.

## Redirect type options

Every redirect rule offers the same choices:

| Option | Behavior |
|---|---|
| None (WordPress Default) | No redirect from this rule. WordPress default behavior applies. |
| Page | Redirect to a published WordPress page you pick. |
| Custom URL | Redirect to any URL you type. |
| Integration Destination | Redirect to a page from an active integration (for example WooCommerce My Account). This option appears only when at least one supported integration is active. |

See the Usage section for the full detail of each surface.
