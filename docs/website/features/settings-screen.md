# The Settings Screen

Open **WB Plugins > Login Redirect**. The screen has a left-hand menu with one entry per tab. The address is `wp-admin/admin.php?page=bp-redirect`.

| Tab | What it is for |
|---|---|
| Overview | Short description of the plugin and links to the documentation, the support center and a feedback form. Nothing to set here. |
| Global Redirection | One login and one logout destination for every user. Off by default. See [Global redirection](../usage/global-redirection.md). |
| User Roles | A login and logout destination for each WordPress role. On by default, with no roles configured. See [Role-based redirection](../usage/role-based-redirection.md). |
| Member Types | A login and logout destination for each BuddyPress member type. Called Profile Types on BuddyBoss. Shown only when at least one type exists. See [BuddyPress member types](../usage/buddypress-member-types.md). |
| FAQ | The common questions, as a list that opens and closes. |
| License | Shows whether automatic updates are enabled. There is no key to enter. See [Installation](../getting-started/installation.md#automatic-updates). |

## How saving works

- Each tab has a **Save Settings** button. The settings are saved without reloading the page.
- If a save fails, the error appears on the page, not in a browser pop-up.
- Each rule, whether it is for a role, a member type or everyone, has the same four choices: None (WordPress Default), Page, Custom URL and Integration Destination.
- The Page choice lists every published page on the site. The list loads once per screen and each choice fills when you open it, so the User Roles tab stays quick on sites with thousands of pages.
- Roles cannot be dragged into a new order, because the order has no effect. Rules are checked in the order described in [What the plugin does](overview.md#priority-chain).

## Other plugins' notices

The plugin hides other plugins' admin notices on its own screen so the settings stay readable. They return on every other admin screen.
