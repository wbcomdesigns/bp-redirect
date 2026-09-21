# Integration API

Integrations supply extra redirect destinations and, optionally, group types (like BuddyPress member types). Each integration is a class that extends `Wbcom_Redirect_Integration` and self-registers with the singleton `Wbcom_Redirect_Integration_Registry`.

## Base class

`Wbcom_Redirect_Integration` (in `integrations/class-wbcom-redirect-integration.php`) defines these methods:

| Method | Required | Purpose |
|---|---|---|
| `get_slug()` | yes | Unique slug, for example `woocommerce` |
| `get_name()` | yes | Human-readable name for the admin UI |
| `is_available()` | yes | Return true when the parent plugin is active |
| `get_destinations()` | yes | Return an array of `Wbcom_Redirect_Destination` objects |
| `resolve_url( $destination_slug, $user )` | yes | Turn a destination slug into a URL, or return false |
| `get_group_types( $user )` | no | Group type slugs the given user belongs to (default: empty) |
| `get_all_group_types()` | no | All group type slug => label pairs for the admin tab (default: empty) |
| `has_admin_tab()` | no | Whether this integration needs its own settings tab (default: true when `get_all_group_types()` is non-empty) |

## Destination value object

`Wbcom_Redirect_Destination` is immutable and constructed as:

```php
new Wbcom_Redirect_Destination(
  'woocommerce.my-account', // slug: "{integration}.{destination}"
  __( 'My Account', 'bp-redirect' ), // label shown in the dropdown
  'woocommerce' // owning integration slug
);
```

The full slug (`{integration}.{destination}`) is what gets stored in a rule's `integration` field and is later split by the resolver.

## Registry

`Wbcom_Redirect_Integration_Registry::instance()` provides:

- `register( $integration )` - add an integration.
- `get( $slug )` - fetch one integration.
- `get_all()` / `get_active()` - all, or only those whose `is_available()` is true.
- `get_all_destinations()` - destinations from all active integrations, used to build the rule dropdown.
- `get_with_admin_tabs()` - active integrations that report `has_admin_tab()`, used to build dynamic settings tabs.

## Adding an integration

1. Create a class extending `Wbcom_Redirect_Integration`, implement the required methods, and self-register in its constructor:

   ```php
   public function __construct() {
       Wbcom_Redirect_Integration_Registry::instance()->register( $this );
   }
   ```

2. Instantiate it at the bottom of the file (the shipped integrations end with `new Wbcom_Redirect_Class();`).
3. Load the file. The core loader in `Wbcom_Redirect::load_integrations()` includes files for a fixed list of slugs (`buddypress`, `woocommerce`, `bbpress`, `dokan`, `learndash`, `peepso`) from the `integrations/{slug}/` folders when the file exists. To add a slug outside that list, require your file from your own plugin or include it before the registry is read.

## Shipped integrations

The six shipped integrations live under `integrations/{slug}/` and follow this pattern exactly. Use `integrations/woocommerce/class-wbcom-redirect-woocommerce.php` as the simplest reference (destinations only, no group types) and `integrations/buddypress/class-wbcom-redirect-buddypress.php` as the reference for group types (member types).
