# What's new in 2.3.0

2.3.0 redesigns the settings screen, makes redirects safer when a target page is gone, and fixes automatic updates. Read the update notes first, because a site on an earlier version may need a manual update.

## Update notes

- **WordPress 6.5 and PHP 8.1 or later are required.** A site on older versions cannot run 2.3.0. Check your hosting PHP version before updating. The plugin is tested up to WordPress 7.1.
- **Sites on 2.2.x or earlier may never have seen an update.** Earlier versions could not find the built-in license key, so update checks failed. Download 2.3.0 from your Wbcom Designs account and install it over the old copy. Updates then work from Plugins as usual. See [Automatic updates](../getting-started/installation.md#automatic-updates).
- **Your redirect rules carry over automatically.** Settings from version 2.0 are converted on the first visit after the update, including a member's first login, not only after an admin visit.
- **Updates now come through the Wbcom Designs update service** with signed packages. No license key is needed.
- **The menu entry is WB Plugins > Login Redirect.**

## What changed for a site owner

- **New settings screen** with a card layout and a left-hand menu: Overview, Global Redirection, User Roles, Member Types, FAQ and License. See [The settings screen](../features/settings-screen.md).
- **The License tab** shows whether automatic updates are enabled, or Off with a reinstall notice when the update files are missing. Administrators also see a notice on other screens in that case.
- **Custom URLs** can point to another domain, and links with query strings arrive intact.
- **The WooCommerce My Account login form** now follows the login rules too.
- **A configured redirect now beats another plugin's default**, such as Dokan sending every customer to My Account.
- **Skipped rules.** A rule whose page is no longer published, or whose BuddyPress component (Activity, Groups) is turned off, is skipped so the next rule applies instead of a missing page. This includes the WooCommerce, Dokan, PeepSo and LearnDash pages a destination opens.
- **Dokan destinations apply to vendors only.** LearnDash Student Dashboard is offered only when the site has a published page with the slug `dashboard`.
- **PeepSo Profile and Activity Stream** are offered only when PeepSo's own page for them exists.
- **bbPress Forums** uses the address bbPress reports. When bbPress gives none, the rule is skipped instead of guessing `/forums/`.
- **The member types tab** is called Member Types on BuddyPress and Profile Types on BuddyBoss, and every type shows its name.
- **The User Roles tab** loads quickly on sites with thousands of pages.
- **Save errors** show on the page instead of a browser pop-up.
- **Layout and accessibility fixes.** Redirect options line up on the User Roles, Global and member type tabs, including on phones. Overview text is no longer cut off. FAQ questions announce whether they are open. The Documentation link opens the plugin's own guide.

## Retest after updating

1. Log in as a user whose role has a rule, and confirm you land on the rule's destination.
2. Log in as a user with no role rule, and confirm the member type rule or the Global rule applies.
3. If you use WooCommerce, log in from the My Account form and confirm the same destination.
4. If a rule uses a Custom URL on another domain, log in and confirm you arrive there with its query string.
5. Log out with a user that has a logout rule.
6. Open the License tab and confirm Automatic updates shows Enabled.

## For developers

- New filter `wbcom_redirect_honor_redirect_to` lets a same-site `redirect_to` on the login link win over the configured login rule. Arguments: `$honor` (default `false`), `$request`, `$user`. See [Architecture and resolution](../developer-guide/architecture.md#extension-points).
- The login and logout filters run at priority 99, and `woocommerce_login_redirect` is hooked at the same priority.
