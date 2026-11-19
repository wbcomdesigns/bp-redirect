# Role-Based Redirection

Role rules set a login and logout destination per WordPress role. A role rule is the highest-priority rule: if a user's role has a rule that resolves to a URL, it wins over member type rules, the Global rule, and the WordPress default.

## Configure

1. Go to WB Plugins > Login Redirect and open the User Roles tab.
2. Make sure Enable User Role Redirection is on. It is on by default, though no per-role destinations are set until you add them.
3. The page lists every role registered on the site as an accordion. Expand a role.
4. Set its Login Redirect and Logout Redirect using the type options (None, Page, Custom URL, or Integration Destination).
5. Select Save Settings.

The role list is built from the site's registered roles, so custom roles added by other plugins appear automatically.

## How a user's role is matched

At login or logout the plugin reads the user's assigned roles and checks each one against the saved role rules. The first role that has a configured rule which resolves to a URL is used. A user with more than one role is matched against their roles in the order WordPress stores them.

## Interaction with other rules

- A role rule set to None (or one that resolves to no URL, for example a deleted page) is treated as "no result", and the plugin continues down the priority chain to member types, then Global, then WordPress default.
- Disabling User Role Redirection turns off all role rules at once without deleting them.

## Example

Send subscribers to a members page after login and keep administrators on the default WordPress behavior:

1. Expand Subscriber, set Login Redirect to Page and choose your members page.
2. Leave Administrator set to None.
3. Save Settings.
