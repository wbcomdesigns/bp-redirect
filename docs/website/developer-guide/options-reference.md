# Options and Data Reference

The plugin stores its settings in the WordPress options table. It does not use the Settings API `register_setting()`; the admin screen saves through a custom AJAX handler that calls `update_option()` directly after a nonce and capability check.

## Options

| Option key | Purpose |
|---|---|
| `wbcom_redirect_global` | Global login and logout rules |
| `wbcom_redirect_roles` | Per-role login and logout rules |
| `wbcom_redirect_{integration_slug}` | Per-group-type rules for an integration, for example `wbcom_redirect_buddypress` |
| `wbcom_redirect_migrated` | Flag set to `1` once the one-time 2.0.0 migration has run |

## Option shapes

Global (`wbcom_redirect_global`):

```php
array(
  'enabled' => 'yes' | 'no',
  'login'   => array( 'type' => ..., 'page_id' => 0, 'custom_url' => '', 'integration' => '' ),
  'logout'  => array( 'type' => ..., 'page_id' => 0, 'custom_url' => '', 'integration' => '' ),
)
```

Roles (`wbcom_redirect_roles`):

```php
array(
  'enabled' => 'yes' | 'no',
  'roles'   => array(
    'subscriber' => array(
      'login'  => array( ... ),
      'logout' => array( ... ),
    ),
    // ... one entry per configured role
  ),
)
```

Integration group types (`wbcom_redirect_{slug}`):

```php
array(
  'enabled' => 'yes' | 'no',
  'groups'  => array(
    'member_type_slug' => array(
      'login'  => array( ... ),
      'logout' => array( ... ),
    ),
  ),
)
```

## Redirect config shape

Every login and logout rule uses the same array:

| Key | Type | Meaning |
|---|---|---|
| `type` | string | One of `none`, `page`, `custom_url`, `integration` |
| `page_id` | int | Target page ID when `type` is `page` |
| `custom_url` | string | Target URL when `type` is `custom_url` |
| `integration` | string | Destination slug like `woocommerce.my-account` when `type` is `integration` |

## Defaults set on activation

- `wbcom_redirect_global` - `enabled` = `no`, both login and logout set to `type` = `none`.
- `wbcom_redirect_roles` - `enabled` = `yes`, `roles` empty (so no user is redirected until a rule is added).

Defaults are written only if the option does not already exist.

## Sanitization

On save, `type` is constrained to the allowed set (anything else becomes `none`), `page_id` is passed through `absint()`, `custom_url` through `esc_url_raw()`, and `integration` through `sanitize_text_field()`, and `enabled` is stored as `yes` or `no` only. The integration scope accepts only integrations that show a types tab. The AJAX handler requires the `wbcom-redirect-admin` nonce and the `manage_options` capability.

## Migration from 2.0.0

On the first `init` after updating (any request, admin or front end), `wbcom_redirect_maybe_migrate()` converts the older options `bp_redirect_admin_settings` (roles), `bp_redirect_admin_settings_global` (global), and `bp_redirect_member_type_admin_settings` (BuddyPress member types) into the new shapes above. A `custom` URL that matches a page permalink becomes a `page` rule; a BuddyPress `referer` component (`profile`, `member_activity`, `groups`) becomes the matching `buddypress.*` integration destination. The migration runs once and then sets `wbcom_redirect_migrated`.
