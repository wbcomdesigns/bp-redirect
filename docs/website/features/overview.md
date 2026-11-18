# What the Plugin Does

BuddyPress Redirect changes where WordPress sends a user immediately after they log in and after they log out. It hooks the standard WordPress `login_redirect` and `logout_redirect` filters, plus the WooCommerce My Account login form, so it works with the default WordPress login form and any login flow that runs those filters. A rule you set wins over the default of other plugins (for example Dokan sending every customer to My Account).

## Core capabilities

- **Global redirect.** One login destination and one logout destination applied to every user.
- **Role-based redirect.** A separate login and logout destination for each WordPress role (Administrator, Editor, Author, Subscriber, and any custom roles present on the site).
- **Member type redirect.** A separate login and logout destination for each BuddyPress member type (BuddyBoss calls them Profile Types), shown only when member types exist.
- **Three destination kinds per rule.** Each rule can point to a WordPress Page, a Custom URL (another domain is fine, query strings are kept), or an Integration Destination.
- **Integration destinations.** When a supported plugin is active, its key pages become one-click destination options.
- **Auto-detection.** Integration options appear only while their parent plugin is active. No configuration is needed to enable detection.
- **Settings migration.** Settings from the previous 2.0.0 format are converted automatically on the first page load after updating, even if that is a member logging in.

## Priority chain

When a user logs in or out, the plugin evaluates rules in this fixed order and uses the first one that produces a URL:

1. User Role rule
2. Integration Group Types rule (for example BuddyPress member types)
3. Global rule
4. WordPress default

A rule is skipped if it is disabled, has no matching entry for the user, or resolves to no URL: for example its page is no longer published, or its BuddyPress component (Activity, Groups) is turned off. If nothing resolves, WordPress default behavior applies.

## Supported integrations and destinations

| Integration | Detected when | Destinations |
|---|---|---|
| BuddyPress | BuddyPress is active | Member Profile (always); Member Activity (if the Activity component is active); Groups Directory (if the Groups component is active); plus per member type rules |
| WooCommerce | WooCommerce is active | My Account, Shop, Checkout, Orders |
| bbPress | bbPress is active | Forums, User Profile |
| Dokan | Dokan is active | Vendor Dashboard, Store Page (vendors only) |
| LearnDash | LearnDash is active | Student Dashboard (when a `dashboard` page exists), Courses |
| PeepSo | PeepSo is active | Profile, Activity Stream |

Destination availability is read live from each integration, so a destination appears only when its underlying feature is present. See Usage > Integration Destinations for the behavior of each destination.
