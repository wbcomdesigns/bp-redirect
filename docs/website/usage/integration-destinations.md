# Integration Destinations

When a supported plugin is active, its key pages become selectable destinations in every redirect rule. Pick Integration Destination in a rule, then choose the destination from the grouped dropdown. The Integration Destination option is hidden entirely when no supported integration is active.

Destinations are resolved live at the moment of redirect using each plugin's own functions, so they always point at the current pages for that site.

## BuddyPress

| Destination | Resolves to | Shown when |
|---|---|---|
| Member Profile | The logging-in user's BuddyPress profile URL | BuddyPress active |
| Member Activity | The user's profile activity page | Activity component active |
| Groups Directory | The Groups directory page | Groups component active |

If the Activity or Groups component is turned off later, a rule pointing at it is skipped. BuddyPress also adds per member type rules. See BuddyPress Member Types.

## WooCommerce

| Destination | Resolves to |
|---|---|
| My Account | The WooCommerce My Account page |
| Shop | The Shop page |
| Checkout | The Checkout page |
| Orders | The Orders endpoint under My Account |

## bbPress

| Destination | Resolves to |
|---|---|
| Forums | The bbPress forums archive URL |
| User Profile | The user's bbPress profile URL |

With BuddyPress active, bbPress sends User Profile to the member's BuddyPress profile; that is bbPress's own behaviour.

## Dokan

| Destination | Resolves to |
|---|---|
| Vendor Dashboard | The Dokan vendor dashboard URL |
| Store Page | The user's Dokan store URL |

Both Dokan destinations apply to vendors only. For a customer the rule is skipped and the next rule applies.

## LearnDash

| Destination | Resolves to |
|---|---|
| Student Dashboard | The published page whose slug is `dashboard` |
| Courses | The `sfwd-courses` post type archive |

Student Dashboard is offered only when the site has a published page with the slug `dashboard`, because LearnDash has no dashboard page of its own.

## PeepSo

| Destination | Resolves to |
|---|---|
| Profile | The user's PeepSo profile URL |
| Activity Stream | The PeepSo activity page |

Each PeepSo destination is offered only while the page assigned in PeepSo > Configuration > Navigation exists and holds PeepSo's shortcode. Next to BuddyPress, PeepSo may point Activity at `/activity/`, which belongs to BuddyPress; PeepSo then shows "Error: page not found!" on that screen. Pick PeepSo's own page there and the destination appears.

## What happens if a destination cannot be resolved

A destination that opens a WordPress page (WooCommerce Shop, My Account, Orders and Checkout, the Dokan Vendor Dashboard, the PeepSo pages, the LearnDash dashboard page) is skipped while that page is in draft, private, trashed or unassigned.


If the underlying plugin function is unavailable or returns nothing (for example the user is not a Dokan vendor, or a required page is missing), the rule produces no URL. The plugin then continues down the priority chain and finally falls back to WordPress default behavior.
