# What the Plugin Does

BuddyPress Redirect changes where WordPress sends a user immediately after they log in and after they log out. It hooks the standard WordPress `login_redirect` and `logout_redirect` filters, so it works with the default WordPress login form and any login flow that runs those filters.

## Core capabilities

- **Global redirect.** One login destination and one logout destination applied to every user.
- **Role-based redirect.** A separate login and logout destination for each WordPress role (Administrator, Editor, Author, Subscriber, and any custom roles present on the site).
- **BuddyPress member type redirect.** A separate login and logout destination for each BuddyPress member type, shown only when BuddyPress is active and member types exist.
- **Three destination kinds per rule.** Each rule can point to a WordPress Page, a Custom URL, or an Integration Destination.
- **Integration destinations.** When a supported plugin is active, its key pages become one-click destination options.
- **Auto-detection.** Integration options appear only while their parent plugin is active. No configuration is needed to enable detection.
- **Settings migration.** Settings from the previous 2.0.0 format are converted automatically on first admin load after updating.

## Priority chain

When a user logs in or out, the plugin evaluates rules in this fixed order and uses the first one that produces a URL:

1. User Role rule
2. Integration Group Types rule (for example BuddyPress member types)
3. Global rule
4. WordPress default

A rule is skipped if it is disabled, has no matching entry for the user, or resolves to an empty URL. If nothing resolves, WordPress default behavior applies.

## Supported integrations and destinations

| Integration | Detected when | Destinations |
|---|---|---|
| BuddyPress | BuddyPress is active | Member Profile (always); Member Activity (if the Activity component is active); Groups Directory (if the Groups component is active); plus per member type rules |
| WooCommerce | WooCommerce is active | My Account, Shop, Checkout, Orders |
| bbPress | bbPress is active | Forums, User Profile |
| Dokan | Dokan is active | Vendor Dashboard, Store Page |
| LearnDash | LearnDash is active | Student Dashboard, Courses |
| PeepSo | PeepSo is active | Profile, Activity Stream |

Destination availability is read live from each integration, so a destination appears only when its underlying feature is present. See Usage > Integration Destinations for the behavior of each destination.
