# WB Login Logout Redirect

Redirect users after login & logout by role — built for BuddyPress, BuddyBoss, WooCommerce, bbPress, Dokan, LearnDash & PeepSo.

[![WordPress](https://img.shields.io/badge/WordPress-5.6%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

[**Get the Plugin**](https://wbcomdesigns.com/downloads/buddypress-redirect/) | [**Community Bundle**](https://wbcomdesigns.com/downloads/buddypress-community-bundle/) | [**All Products**](https://store.wbcomdesigns.com/)

---

## Overview

**WB Login Logout Redirect** is a free WordPress plugin that lets you control where users are redirected after login and logout based on their roles.

Works **completely standalone** — no third-party plugins required. When BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, or PeepSo are active, additional redirect destinations automatically become available.

## Features

- **Global Redirect** — Set a default login/logout redirect for all users
- **Role-Based Redirect** — Configure separate redirects per user role (Administrator, Editor, Subscriber, etc.)
- **Page or Custom URL** — Redirect to any WordPress page or enter a custom URL
- **Integration Destinations** — Redirect to BuddyPress profiles, WooCommerce My Account, bbPress forums, and more
- **Priority Chain** — Role > Integration Group Types > Global > WordPress Default
- **Auto-Detection** — Integration options appear only when their parent plugin is active
- **BuddyPress Member Types** — Configure redirects per member type
- **Clean Admin UI** — Unified settings interface with accordion-style role management

## Supported Integrations

| Integration | Destinations |
|-------------|-------------|
| **BuddyPress / BuddyBoss** | Member Profile, Member Activity, Groups Directory, Member Types |
| **WooCommerce** | My Account, Shop, Checkout, Orders |
| **bbPress** | Forums, User Profile |
| **Dokan** | Vendor Dashboard, Store Page |
| **LearnDash** | Student Dashboard, Courses |
| **PeepSo** | Profile, Activity Stream |

## Installation

1. Upload the `bp-redirect` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Go to **WB Plugins > Redirect** to configure settings

## How It Works

1. **Install & Activate** — Plugin works immediately with WordPress defaults
2. **Configure Global Redirect** — Set where all users go after login/logout
3. **Configure Role Redirects** — Override globals per role (e.g., send Subscribers to their profile, Vendors to their dashboard)
4. **Integration Destinations** — When BuddyPress/WooCommerce/etc. are active, their pages appear as destination options

## Redirect Priority

```
User Role > Integration Group Types (e.g. BuddyPress member types) > Global > WordPress Default
```

## FAQ

**Does this plugin require BuddyPress?**
No. It works standalone on any WordPress site. Integration support is auto-enabled when compatible plugins are active.

**What happens if no redirect is configured?**
WordPress default behavior applies — admins go to the dashboard, other users go to the home page.

**Can I redirect WooCommerce customers to My Account after login?**
Yes. When WooCommerce is active, "My Account", "Shop", "Checkout", and "Orders" appear as destination options.

## More Products by Wbcom Designs

Building a community or membership site? Check out our other products at [**store.wbcomdesigns.com**](https://store.wbcomdesigns.com/):

| Product | Description |
|---------|-------------|
| [**WPMediaVerse**](https://store.wbcomdesigns.com/) | Complete media management for WordPress communities |
| [**Jetonomy**](https://store.wbcomdesigns.com/) | Token-based economy and rewards for your community |
| [**WP Sell Services**](https://store.wbcomdesigns.com/) | Sell and manage services directly from WordPress |
| [**WP Career Board**](https://store.wbcomdesigns.com/) | Job board and career portal for WordPress |
| [**SnipShare**](https://store.wbcomdesigns.com/) | Code snippet sharing platform for developers |
| [**WB Member Wiki**](https://store.wbcomdesigns.com/) | Collaborative wiki system for community members |
| [**Dashboard for LearnDash**](https://store.wbcomdesigns.com/) | Enhanced student and instructor dashboard for LearnDash |

Save more with the [**BuddyPress Community Bundle**](https://wbcomdesigns.com/downloads/buddypress-community-bundle/) — get all our BuddyPress add-ons in one package.

## Support

Need help? [Contact us](https://wbcomdesigns.com/contact/) for support or custom development.

## License

GPLv2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
