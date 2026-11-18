# Troubleshooting

## My redirect does not happen at all

Work down the checklist:

1. Confirm the rule you set is enabled. Each surface has its own toggle: Enable Global Redirection, Enable User Role Redirection, and Enable Member Type Redirection (Profile Type on BuddyBoss). A disabled surface is skipped.
2. Remember the priority order. A higher-priority rule that resolves wins. If a role rule resolves, your Global rule will not run for that user. Order: User Role, then member type, then Global, then WordPress default.
3. Confirm the rule is not set to None. None means "do not redirect from this rule".
4. Confirm the target still exists. A Page rule pointing at an unpublished, trashed or deleted page, or a Custom URL left blank, is skipped and the next rule applies.

## The redirect works from the login form but not from another login method

The plugin hooks the standard WordPress `login_redirect` and `logout_redirect` filters. Login or logout flows that do not run those filters (some social login, single sign-on, or custom auth flows) will not trigger the redirect. Check whether the other flow applies those filters.

## An Integration Destination is selected but the user is not redirected there

The destination is resolved live at redirect time using the integration's own functions. It returns no URL, and the plugin falls back to WordPress default, when:

- the parent plugin is no longer active, or
- the required page or context does not exist for that user (for example the user is not a Dokan vendor, so Vendor Dashboard and Store Page are skipped), or
- a required component is off (for example BuddyPress Member Activity needs the Activity component; Groups Directory needs the Groups component).

## LearnDash "Student Dashboard" is not in the list

LearnDash has no dashboard page of its own, so Student Dashboard is offered only when the site has a published page with the slug `dashboard`. Create that page, or use a Page rule pointing at your dashboard.

## Settings did not carry over after updating

The one-time migration from the 2.0.0 format runs on the first page load after the update (`init`) and is marked complete with the `wbcom_redirect_migrated` option. If settings look empty, check that option: if it is set and the rules are empty, the old options held no rules.

## Admin notices from other plugins disappear on the settings page

This is intentional. The plugin hides other admin notices on its own settings pages so the redirect settings stay readable. Notices return on every other admin screen.

## The plugin says the update files are missing

The `vendor/easy-digital-downloads` folder was removed (often by a file sync or a security plugin). Redirects keep working, but automatic updates are off. Reinstall the plugin from your Wbcom Designs account.
