# Plugin: WB Login Logout Redirect

> **READ FIRST:** [`audit/manifest.json`](audit/manifest.json) is the canonical inventory — 0 REST, 1 AJAX (custom settings save), 1 admin page (dynamic tabs), 3 option groups, 0 CPTs, 0 cron, 6 integrations; consumes `login_redirect` + `logout_redirect`. See also [`audit/wppqa-baseline-2026-06-04/SUMMARY.md`](audit/wppqa-baseline-2026-06-04/SUMMARY.md). Refresh via `/wp-plugin-onboard --refresh` after non-trivial changes.

## Quick reference
- **Main file**: `bp-redirect.php` (slug `bp-redirect`, display name "WB Login Logout Redirect")
- **Version**: `2.3.0` (branch `2.3.0`, based on the **`v2.2.0` release tag** — see branch note below)
- **Text domain**: `bp-redirect`
- **Prefix**: `wbcom_redirect` / `WBCOM_REDIRECT_`; admin UI uses `wbredir` / `--wbredir-admin-*`
- **Requires**: WP 6.5+, PHP 8.1+. Optional: BuddyPress / BuddyBoss / WooCommerce / bbPress / Dokan / LearnDash / PeepSo.

## ⚠️ Branch topology (important)
The repo has **diverged lineages**. As of 2026-06-04:
- `origin/master` — 2.1.0, name "Wbcom Designs - Login Logout Redirect", chronologically newest, has a CI workflow. **248 commits diverged** from the v2.2.0 line.
- `v2.2.0` tag — 2.2.0, name "WB Login Logout Redirect", the **released** line (renamed plugin + added EDD SL SDK). 254 commits diverged from master.
- `origin/wpcs-fixes` — 2.1.0, WPCS/branding work.

The 2.3.0 card-panel migration was based on **`v2.2.0`** (owner's choice). If resuming, confirm which lineage is canonical before branching.

## What it does
Redirects users after login/logout — globally, by user role, or by integration group type (e.g. BuddyPress member types). Resolution: `Wbcom_Redirect_Resolver` + `login_redirect`/`logout_redirect` filters.

## Key entry points
- Orchestrator: `includes/class-wbcom-redirect.php` (`Wbcom_Redirect`, loader-based).
- **Admin (card panel, 2.3.0+)**: `admin/class-wbcom-redirect-admin.php` (`Wbcom_Redirect_Admin`) — one class: shell chrome (`render_settings_page`/`render_tabs`), tab content renderers (`render_global_tab`, `render_roles_tab`, `render_integration_tab`, the shared `render_redirect_form`, welcome, faq, license), and the AJAX save.
- Hub: `admin/views/hub.php` (`render_hub` + `takeover_hub_landing` at admin_menu:999).
- Admin assets: `assets/css/admin.css` (card-panel shell, `--wbredir-admin-*`) + `assets/css/wbcom-redirect-content.css` (relocated from the former admin/wbcom wrapper — styles the tab content) + `admin/assets/css/bp-redirect-admin.css` + `admin/assets/js/wbcom-redirect-admin.js`.
- Integration framework: `integrations/class-wbcom-redirect-integration*.php` + registry; modules under `integrations/{buddypress,woocommerce,bbpress,dokan,learndash,peepso}/`.
- Public: `public/class-wbcom-redirect-public.php`.

## Architecture notes (2.3.0 card-panel migration)
- Migrated off the legacy `admin/wbcom/` wrapper onto a **self-contained** card-panel shell. **UX-only**: every option key, the custom AJAX save handler, the integration registry, and the EDD SDK wiring are unchanged.
- **Settings save = custom AJAX** (`wbcom_redirect_save_settings` → `ajax_save_settings`), NOT the Settings API. Three option groups: `wbcom_redirect_global`, `wbcom_redirect_roles`, `wbcom_redirect_{integration_slug}`. The card-panel form posts to the same handler; the per-scope sanitizers are in `ajax_save_settings` + `sanitize_config`.
- **Tab content** uses `.wbcom-*` classes styled by `assets/css/wbcom-redirect-content.css` (relocated from the wrapper so the dir could be deleted). The **chrome** uses `.wbredir-*` (new shell). Two stylesheets by design.
- **EDD = official EDD SL SDK** (`vendor/easy-digital-downloads/edd-sl-sdk`, item_id 80951, preset key = free auto-updates). **KEEP it** — do not replace with the Contact Me custom client (portfolio policy). License tab = update status only, no key field.
- Removed `admin/wbcom`, the `[wbcom_admin_setting_header]` shortcode call, the wrapper require, the dead admin/wbcom grunt sub-tasks, and repointed the `includes/functions.php` asset resolver off admin/wbcom.

## Redirect rules worth knowing
- Every login/logout goes through `Wbcom_Redirect_Public::allow()`: a destination this plugin resolved (custom URL, page, integration) has its host added to `allowed_redirect_hosts` for that one request, so a Custom URL on another domain is followed. WordPress' own `redirect_to` (which can come from the visitor) is never whitelisted. URLs stay raw (`esc_url()` would turn `&` into `&#038;`).
- The WooCommerce My Account login form fires `woocommerce_login_redirect`, not `login_redirect`; both are hooked.
- Page dropdowns: `render_redirect_form()` prints the page list once per screen in `<template id="wbcom-redirect-page-options">`; each select carries only its saved page and is filled from the template on first focus. Keep it that way - per-select page lists made the User Roles tab O(roles x pages).

## Gotchas
- **`vendor/` holds local composer WPCS tooling** and is gitignored except the tracked EDD SL SDK submodule (`vendor/easy-digital-downloads/edd-sl-sdk`). Still stage files explicitly.
- **Build**: `npm ci && npx grunt build` (rtlcss + cssmin + uglify over `admin/assets/{css,js}`). Production loads `bp-redirect-admin.min.css` / `-rtl.min.css`, so rebuild after any CSS edit. `assets/css/admin.css` and `wbcom-redirect-content*.css` load unminified.
- Third-party admin notices are removed in PHP (`hide_admin_notices`); don't hide `.notice-error` in CSS or the settings save error disappears too.
