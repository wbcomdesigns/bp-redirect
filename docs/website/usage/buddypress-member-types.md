# BuddyPress Member Types

When BuddyPress is active and the site has at least one registered member type, a "BuddyPress Types" tab appears under WB Plugins > Redirect. It lets you set a login and logout destination for each member type.

Member type rules sit between role rules and the Global rule in the priority chain: they apply only when no role rule matched the user, and they override the Global rule.

## Configure

1. Go to WB Plugins > Redirect and open the BuddyPress Types tab. The tab is present only when member types exist.
2. Turn on Enable BuddyPress Type Redirection.
3. Each member type is listed as an accordion. Expand a type and set its Login Redirect and Logout Redirect using the type options (None, Page, Custom URL, or Integration Destination).
4. Select Save Settings.

If BuddyPress is active but no member types are registered, the tab shows a note explaining that types must be created in BuddyPress first.

## How a user's member type is matched

At login or logout the plugin reads the logging-in user's assigned member types and checks each against the saved member type rules. The first type with a rule that resolves to a URL is used. A user with more than one member type is matched against them in order.

## Priority reminder

1. User Role rule
2. BuddyPress member type rule
3. Global rule
4. WordPress default

A member type rule only takes effect when the user's role did not already produce a redirect.
