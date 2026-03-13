## QA Testing Note — Login Logout Redirect v2.1.0

**Branch:** `2.1.0`
**Plugin slug:** `bp-redirect`
**What changed:** Complete rebuild from BuddyPress-dependent plugin to standalone WordPress plugin with modular integration support.

### Test Environment Setup

- WordPress 6.4+ (tested up to 6.9)
- PHP 7.4+
- Create test users with different roles: **Administrator**, **Editor**, **Subscriber** (minimum)
- Create 2-3 published pages for redirect targets (e.g. "Welcome Page", "Members Area", "Thank You")
- **Optional:** Activate BuddyPress to test integration tab and member type redirects
- **Optional:** Activate WooCommerce to test integration destinations

---

### Checklist

#### 1. Activation & Deactivation

| # | Test | Expected | Pass |
|---|------|----------|------|
| 1.1 | Activate plugin (no BuddyPress active) | Plugin activates without errors or warnings | |
| 1.2 | Check redirect after activation | Redirects to plugin settings page | |
| 1.3 | Deactivate and reactivate | No errors, settings preserved | |
| 1.4 | Check Plugins page | "Settings" and "Support" links visible under plugin name | |

#### 2. Admin UI — Tab Navigation

| # | Test | Expected | Pass |
|---|------|----------|------|
| 2.1 | Navigate to WB Plugins > Redirect | Settings page loads with tabs | |
| 2.2 | Click each tab: Welcome, Global, User Roles, FAQ | Each tab loads without errors | |
| 2.3 | Check plugin name and version in header | Shows "Login Logout Redirect" and "Version 2.1.0" | |
| 2.4 | If BuddyPress active with member types | "BuddyPress Types" tab appears between User Roles and FAQ | |
| 2.5 | If BuddyPress inactive | No BuddyPress tab visible | |

#### 3. Global Redirection Tab

| # | Test | Expected | Pass |
|---|------|----------|------|
| 3.1 | Toggle "Enable Global Redirection" ON | Login/Logout fields slide down | |
| 3.2 | Toggle OFF | Fields slide up and hide | |
| 3.3 | Set Login to "Page" > select a page > Save | Success notice appears, fades after 3 seconds | |
| 3.4 | Reload the page | Saved values persist (radio = Page, correct page selected) | |
| 3.5 | Set Login to "Custom URL" > enter `https://example.com` > Save | Saves successfully | |
| 3.6 | Reload | Custom URL value persists in input field | |
| 3.7 | Set Login to "None" > Save > Reload | Radio shows "None" selected | |
| 3.8 | If BuddyPress/WooCommerce active | "Integration Destination" radio option appears with dropdown | |
| 3.9 | If no integrations active | "Integration Destination" option is hidden | |
| 3.10 | Repeat 3.3-3.7 for Logout section | Same behavior | |

#### 4. User Roles Tab

| # | Test | Expected | Pass |
|---|------|----------|------|
| 4.1 | Toggle "Enable User Role Redirection" ON | Accordion section appears | |
| 4.2 | Click on "Administrator" accordion header | Expands to show Login/Logout redirect options | |
| 4.3 | Click on "Subscriber" accordion header | Subscriber section expands, Administrator collapses | |
| 4.4 | Set Administrator Login to "Page" > select page | Radio and dropdown update correctly | |
| 4.5 | Set Subscriber Login to "Custom URL" > enter URL | Input field appears and accepts URL | |
| 4.6 | Click Save | Success notice appears | |
| 4.7 | Reload page, expand both roles | Both saved values persist correctly | |
| 4.8 | Set different redirects for Login and Logout on same role | Both save independently | |

#### 5. FAQ Tab

| # | Test | Expected | Pass |
|---|------|----------|------|
| 5.1 | Click first FAQ question | Answer panel expands (slides down) | |
| 5.2 | Click same question again | Panel collapses | |
| 5.3 | Click a different question | New panel expands | |
| 5.4 | Verify all 6 FAQ items expand/collapse | All work correctly | |

#### 6. Login Redirect (Functional)

**Important:** Log out completely before each test. Use an incognito/private window.

| # | Test | Expected | Pass |
|---|------|----------|------|
| 6.1 | Set Global Login to a specific page, enable it. Log in as any user | Redirects to that page after login | |
| 6.2 | Set Role redirect for "Subscriber" to a different page, enable Roles. Log in as Subscriber | Redirects to the role-specific page (role overrides global) | |
| 6.3 | Log in as Administrator (no role redirect set) | Falls through to Global redirect | |
| 6.4 | Disable both Global and Roles | Login goes to WordPress default (wp-admin for admins) | |
| 6.5 | Set Role redirect to "Custom URL" with external URL. Log in | Redirects to that external URL | |
| 6.6 | If BuddyPress active: Set role redirect to "Integration > Member Profile". Log in | Redirects to the user's BuddyPress profile | |
| 6.7 | If WooCommerce active: Set to "Integration > My Account". Log in | Redirects to WooCommerce My Account page | |

#### 7. Logout Redirect (Functional)

| # | Test | Expected | Pass |
|---|------|----------|------|
| 7.1 | Set Global Logout to a specific page. Log out | Redirects to that page after logout | |
| 7.2 | Set Role-specific logout for Subscriber. Log out as Subscriber | Redirects to role-specific page | |
| 7.3 | Disable all logout redirects. Log out | Goes to WordPress default logout page | |

#### 8. Priority Chain

| # | Test | Expected | Pass |
|---|------|----------|------|
| 8.1 | Enable Global (Page A) + Role/Subscriber (Page B). Log in as Subscriber | Goes to Page B (role wins) | |
| 8.2 | Same setup. Log in as Editor (no role redirect) | Goes to Page A (falls to global) | |
| 8.3 | Disable Global, keep Role. Log in as Editor | Goes to WordPress default (no match) | |
| 8.4 | If BP active with member types: Set member type redirect + role redirect for same user | Role redirect takes priority over member type | |

#### 9. Integration Tab — BuddyPress Member Types (if BP active)

| # | Test | Expected | Pass |
|---|------|----------|------|
| 9.1 | BuddyPress Types tab shows all registered member types | Each type listed in accordion | |
| 9.2 | Set Login redirect for a member type > Save > Reload | Values persist | |
| 9.3 | Assign a user to that member type. Log in as that user | Redirects according to member type setting (if no role override) | |

#### 10. Migration from v2.0.0 (if applicable)

| # | Test | Expected | Pass |
|---|------|----------|------|
| 10.1 | Install v2.0.0, configure settings, then update to v2.1.0 | Plugin activates without errors | |
| 10.2 | Check Global tab | Old global settings migrated correctly | |
| 10.3 | Check User Roles tab | Old role settings migrated correctly | |
| 10.4 | Check BP Member Types tab (if BP active) | Old member type settings migrated | |

#### 11. Edge Cases

| # | Test | Expected | Pass |
|---|------|----------|------|
| 11.1 | Save with no changes made | Success notice, no errors | |
| 11.2 | Enter invalid URL in Custom URL field | Browser validation prevents saving non-URL value | |
| 11.3 | Select "Page" but don't choose a page, Save | Saves with type=page but page_id=0, falls through to default on login | |
| 11.4 | Multiple browser tabs with admin open | Each saves independently without conflicts | |
| 11.5 | RTL language active | Admin UI renders correctly in RTL | |

---

### Known Behaviors

- Plugin slug remains `bp-redirect` for backward compatibility — this is intentional.
- Integration destinations (BuddyPress, WooCommerce, etc.) only appear when those plugins are active. Deactivating an integration plugin gracefully hides its destinations.
- The dismiss "X" on save notice works. Notice also auto-fades after 3 seconds.

### Not In Scope for v2.1.0

- bbPress, Dokan, LearnDash, PeepSo integration destinations are registered but the redirect URL resolution can only be tested if those plugins are installed. Verify at minimum that the plugin doesn't error when these plugins are absent.
