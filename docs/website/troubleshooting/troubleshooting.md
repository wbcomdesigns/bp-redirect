# Troubleshooting

## My redirect does not happen at all

Work down the checklist:

1. Confirm the rule you set is enabled. Each surface has its own toggle: Enable Global Redirection, Enable User Role Redirection, and Enable BuddyPress Type Redirection. A disabled surface is skipped.
2. Remember the priority order. A higher-priority rule that resolves wins. If a role rule resolves, your Global rule will not run for that user. Order: User Role, then member type, then Global, then WordPress default.
3. Confirm the rule is not set to None. None means "do not redirect from this rule".
4. Confirm the target still exists. A Page rule pointing at an unpublished or deleted page, or a Custom URL left blank, produces no URL and the plugin falls through to WordPress default.

## The redirect works from the login form but not from another login method

The plugin hooks the standard WordPress `login_redirect` and `logout_redirect` filters. Login or logout flows that do not run those filters (some social login, single sign-on, or custom auth flows) will not trigger the redirect. Check whether the other flow applies those filters.

## An Integration Destination is selected but the user is not redirected there

The destination is resolved live at redirect time using the integration's own functions. It returns no URL, and the plugin falls back to WordPress default, when:

- the parent plugin is no longer active, or
- the required page or context does not exist for that user (for example the user is not a Dokan vendor, so Store Page cannot resolve), or
- a required component is off (for example BuddyPress Member Activity needs the Activity component; Groups Directory needs the Groups component).

## LearnDash "Student Dashboard" does not redirect

The Student Dashboard destination resolves only when a WordPress page with the slug `dashboard` exists. If your LearnDash dashboard uses a different slug, or the dashboard is not a standalone page with that slug, the destination returns no URL. As a workaround, use a Page or Custom URL rule pointing at your actual dashboard page. See the BUGS note reported to the maintainers about this destination.

## bbPress "Forums" points to the wrong place on some setups

When the bbPress helper function is available the forums URL is correct. On setups that fall back to the custom root slug option, the resolved URL can be wrong. If Forums does not land where you expect, use a Page or Custom URL rule pointing at your forums page.

## Settings did not carry over after updating

The one-time migration from the 2.0.0 format runs on the first admin page load after the update and is marked complete with the `wbcom_redirect_migrated` option. If settings look empty, open any admin page once as an administrator to let the migration run, then recheck WB Plugins > Redirect.

## Admin notices from other plugins disappear on the settings page

This is intentional. The plugin hides other admin notices on its own settings pages so the redirect settings stay readable. Notices return on every other admin screen.
