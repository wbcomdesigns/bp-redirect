# Global Redirection

The Global rule sets one login destination and one logout destination for every user on the site. It is the lowest-priority rule that still produces a URL, so it applies only when no role rule and no member type rule matches the user.

## Configure

1. Go to WB Plugins > Redirect and open the Global Redirection tab.
2. Turn on Enable Global Redirection. The login and logout fields appear only while this is on.
3. Set the Login Redirect and the Logout Redirect using the redirect type options below.
4. Select Save Settings. The setting is stored immediately over AJAX; a "Settings saved" notice confirms it.

## Redirect type options

Both the login and logout rows offer the same four choices:

| Option | What to enter | Result |
|---|---|---|
| None (WordPress Default) | Nothing | This rule does not redirect. |
| Page | Pick a published page from the dropdown | Redirects to that page's permalink. |
| Custom URL | A full URL, for example `https://example.com/welcome/` | Redirects to that URL, including one on another domain. Query strings are kept. |
| Integration Destination | Pick a destination from the grouped dropdown | Redirects to the resolved integration page. Shown only when a supported integration is active. |

## Default state

On a fresh install the Global rule is disabled and both login and logout are set to None. Nothing is redirected globally until you enable the rule and choose a destination.

## Notes

- If the selected Page is later unpublished, trashed or deleted, or a Custom URL is left blank, the rule is skipped and the next rule applies (in the end, WordPress default behavior).
- The Global rule never overrides a role or member type rule. If a user matches one of those and it resolves, the Global rule is not used for that user.
