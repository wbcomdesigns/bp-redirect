# Architecture and Resolution

This page describes how the plugin loads, which WordPress hooks it uses, and how a redirect URL is resolved.

## Bootstrap

- The main file `bp-redirect.php` defines constants (`WBCOM_REDIRECT_VERSION`, `WBCOM_REDIRECT_PLUGIN_PATH`, `WBCOM_REDIRECT_PLUGIN_URL`, `WBCOM_REDIRECT_PLUGIN_FILE`, `WBCOM_REDIRECT_PLUGIN_BASENAME`, plus `BP_REDIRECT_*` backward-compatible aliases).
- On `plugins_loaded` it instantiates `Wbcom_Redirect` and calls `run()`. There is no third-party dependency gate; the plugin always boots.
- `Wbcom_Redirect` loads dependencies, registers the six integration files (only if the file exists), and registers admin and public hooks through `Wbcom_Redirect_Loader`.
- Text domain: `bp-redirect`. The plugin relies on WordPress automatic translation loading (no explicit `load_plugin_textdomain()` call).

## WordPress hooks the plugin registers

| Hook | Type | Handler | Purpose |
|---|---|---|---|
| `plugins_loaded` | action | `wbcom_redirect_init` | Boot the plugin |
| `login_redirect` | filter (priority 99, 3 args) | `Wbcom_Redirect_Public::handle_login_redirect` | Rewrite the post-login URL |
| `logout_redirect` | filter (priority 99, 3 args) | `Wbcom_Redirect_Public::handle_logout_redirect` | Rewrite the post-logout URL |
| `woocommerce_login_redirect` | filter (priority 99, 2 args) | `Wbcom_Redirect_Public::handle_woocommerce_login_redirect` | Same rules for the My Account login form |
| `admin_menu` | action | `Wbcom_Redirect_Admin::add_menu` | Add the WB Plugins > Login Redirect page |
| `admin_init` | action | `Wbcom_Redirect_Admin::init_settings` | Build the settings tabs |
| `init` | action | `wbcom_redirect_maybe_migrate` | One-time migration from 2.0.0 settings |
| `admin_enqueue_scripts` | action | `enqueue_styles`, `enqueue_scripts` | Load admin CSS and JS on plugin pages only |
| `in_admin_header` | action | `hide_admin_notices` | Suppress other plugins' notices on plugin pages |
| `wp_ajax_wbcom_redirect_save_settings` | action | `ajax_save_settings` | Save settings over AJAX |
| `activated_plugin` | action | `wbcom_redirect_activation_redirect` | Redirect to settings after activation |
| `plugin_action_links_{basename}` | filter | `wbcom_redirect_plugin_links` | Add Settings and Support links |

Activation (`register_activation_hook`) runs `Wbcom_Redirect_Activator::activate()` to seed defaults. Deactivation runs a no-op.

## Resolution chain

`Wbcom_Redirect_Public` receives the WordPress redirect URL and the `WP_User`. If the user is a `WP_Error` or empty, it returns the original URL unchanged. Otherwise it delegates to `Wbcom_Redirect_Resolver`, which tries, in order:

1. `try_role_redirect()` - reads `wbcom_redirect_roles`; if enabled, matches the user's roles against saved rules.
2. `try_integration_group_redirect()` - iterates active integrations, reads `wbcom_redirect_{slug}`; if enabled, matches the user's group types (for example BuddyPress member types) against saved rules.
3. `try_global_redirect()` - reads `wbcom_redirect_global`; if enabled, uses the login or logout rule.
4. Returns the original WordPress URL if nothing resolved.

Each rule is turned into a URL by `resolve_config()`, which switches on the rule `type`:

- `page` - `get_permalink( page_id )`, only while the page is published
- `custom_url` - the stored URL
- `integration` - splits the destination slug on `.` into `{integration}.{destination}`, looks up the integration in the registry, verifies it is available, and calls its `resolve_url()`
- `none` (or unknown) - returns false

Resolved URLs are passed through `esc_url_raw()` in the resolver. The public handler adds the destination host to `allowed_redirect_hosts` for that one request, so a Custom URL on another domain is followed; a visitor-supplied `redirect_to` is never added. Filters run at priority 99 so a configured rule beats another plugin's default; a rule set to None returns the incoming URL unchanged.

## Extension points

`wbcom_redirect_honor_redirect_to` (filter, default `false`; args `$honor`, `$request`, `$user`): return true to let a same-site `redirect_to` on the login link win over the configured login rule. Apart from this, the redirect logic exposes no custom hooks. The only `do_action` calls in the codebase (`wbcom_add_header_menu`, `wbcom_add_plugin_license_code`) belong to the shared Wbcom admin header wrapper, not to the redirect feature. To add a new destination source, register an integration with the registry (see Integration API).
