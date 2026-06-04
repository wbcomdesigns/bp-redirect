# Plugin: WB Login Logout Redirect

> **READ FIRST:** [`audit/manifest.json`](audit/manifest.json) is the canonical inventory — 0 REST, 1 AJAX (custom settings save), 1 admin page (dynamic tabs), 3 option groups, 0 CPTs, 0 cron, 6 integrations; consumes `login_redirect` + `logout_redirect`. See also [`audit/wppqa-baseline-2026-06-04/SUMMARY.md`](audit/wppqa-baseline-2026-06-04/SUMMARY.md). Refresh via `/wp-plugin-onboard --refresh` after non-trivial changes.

## Quick reference
- **Main file**: `bp-redirect.php` (slug `bp-redirect`, display name "WB Login Logout Redirect")
- **Version**: `2.3.0` (branch `2.3.0`, based on the **`v2.2.0` release tag** — see branch note below)
- **Text domain**: `bp-redirect`
- **Prefix**: `wbcom_redirect` / `WBCOM_REDIRECT_`; admin UI uses `wbredir` / `--wbredir-admin-*`
- **Requires**: WP 5.6+, PHP 7.4+. Optional: BuddyPress / BuddyBoss / WooCommerce / bbPress / Dokan / LearnDash / PeepSo.

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

## Gotchas
- **`vendor/` is untracked and NOT gitignored** — it holds local composer WPCS tooling (php-parallel-lint, squizlabs, wp-coding-standards, …). NEVER `git add -A`; stage migration files explicitly. (The EDD SL SDK under `vendor/easy-digital-downloads/edd-sl-sdk` is a tracked submodule.)
- Admin JS (`admin/assets/js/wbcom-redirect-admin.js:102,107`) still uses `alert()` on the AJAX save error path — pre-existing, flagged by wppqa Rule 10; candidate for a follow-up toast pass.

## Recent changes
| Date | Type | Description | Files |
|---|---|---|---|
| 2026-06-04 | refactor | v2.3.0 card-panel admin migration (self-contained shell + relocated content CSS), kept EDD SL SDK + custom AJAX save; removed admin/wbcom | `admin/class-wbcom-redirect-admin.php`, `admin/views/hub.php`, `assets/css/*`, `includes/class-wbcom-redirect.php`, `includes/functions.php`, `gruntfile.js`, `bp-redirect.php`, `readme.txt` |
| 2026-06-04 | docs | Onboarded — `audit/manifest.json` + wppqa baseline + this file | `audit/`, `CLAUDE.md` |
