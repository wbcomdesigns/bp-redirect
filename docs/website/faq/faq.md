# Frequently Asked Questions

## Does this plugin require BuddyPress?

No. It works standalone on any WordPress site. BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, and PeepSo support is enabled automatically when those plugins are active.

## What is the redirect priority order?

User Role redirect first, then Integration Group Types (for example BuddyPress member types), then Global redirect, then WordPress default. The first rule that resolves to a URL is used.

## What happens if no redirect is configured?

WordPress default behavior applies. Administrators go to the dashboard and other users go to the default location WordPress chose.

## Can I use role and BuddyPress member type redirects together?

Yes. Role-based redirects take priority. If no role rule matches or resolves for a user, the plugin checks their BuddyPress member type, then the Global rule.

## Can I redirect WooCommerce customers to My Account after login?

Yes. When WooCommerce is active, My Account, Shop, Checkout, and Orders appear as Integration Destination options in every rule. Rules also apply when customers log in through the My Account login form.

## Can a login link send people back to the page they came from?

By default the rule you set wins. A developer can let a same-site `redirect_to` win with the `wbcom_redirect_honor_redirect_to` filter (see Developer Guide > Architecture).

## Which integrations are supported?

BuddyPress (Member Profile, Member Activity, Groups Directory, plus member types), WooCommerce (My Account, Shop, Checkout, Orders), bbPress (Forums, User Profile), Dokan (Vendor Dashboard, Store Page), LearnDash (Student Dashboard, Courses), and PeepSo (Profile, Activity Stream).

## Will my previous settings be preserved after updating?

Yes. On the first page load after updating (an admin visit or a member logging in), settings saved in the earlier 2.0.0 format are converted automatically to the current format. See Developer Guide > Options and Data Reference for what is migrated.

## Why is the Integration Destination option missing from my rules?

That option only appears when at least one supported integration is active. On a plain WordPress site with none of the supported plugins active, rules offer None, Page, and Custom URL only.

## Why is there no Member Types tab?

That tab appears only when BuddyPress or BuddyBoss is active and at least one member type is registered. On BuddyBoss it is called Profile Types. Create the types first.

## Do I need a license key?

No. The plugin is free and updates itself through the Wbcom Designs update service. The License tab only shows whether automatic updates are enabled.

## What happens to my rules if I delete the plugin?

Nothing is removed. The rule options stay in the database, so reinstalling the plugin brings your rules back. See [Installation](../getting-started/installation.md#removing-the-plugin).

## Does the plugin store personal data?

No. It stores only your redirect rules, as options. It stores nothing about individual users.

## Where do I get support?

Visit the Wbcom Designs support center at https://wbcomdesigns.com/support/ or the contact page at https://wbcomdesigns.com/contact/.
