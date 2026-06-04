# wppqa Baseline — WB Login Logout Redirect 2.3.0

**Generated:** 2026-06-04 (post card-panel admin migration, branch `2.3.0` off `v2.2.0`).

## `wppqa_check_plugin_dev_rules` — 7 passed / 2 failed / 2 warnings

| Finding | Severity | File | Verdict |
|---|---|---|---|
| `alert()` used | high | `admin/assets/js/wbcom-redirect-admin.js:102` | **Pre-existing** — AJAX save error path. Not introduced by the migration. Deferred to a follow-up toast pass. |
| `alert()` used | high | `admin/assets/js/wbcom-redirect-admin.js:107` | **Pre-existing** — same. |
| 7 distinct breakpoints | medium (warn) | combined CSS | Advisory — the relocated content CSS + the shared card-panel shell CSS each define breakpoints. Not blocking. |
| 36px button < 40px tap target | low (warn) | `assets/css/admin.css:556` | Inherited from the shared Wbcom card-panel CSS — portfolio-wide nit, fix once across the shared stylesheet. |

`wppqa_check_rest_js_contract` / `wppqa_check_wiring_completeness`: not applicable (no REST controllers; settings save via custom AJAX, read by the front-end resolver — no `templates/` tree).

## Verdict

The card-panel migration introduces **no new** high-severity findings. The 2 `alert()` calls are in the plugin's own retained admin JS (AJAX save error path), pre-date this migration, and are tracked here for a future toast pass (would pair with adding a `wbredirToast` helper). The migration itself was browser-verified clean: all five tabs render, the Global AJAX save round-trips (`wbcom_redirect_global` persisted), role + integration accordions work, License tab shows EDD SDK auto-update status, zero console errors.

## EDD note
This plugin ships the **official EDD Software Licensing SDK** (`vendor/easy-digital-downloads/edd-sl-sdk`, item_id 80951, preset key for free auto-updates) — kept as-is, NOT replaced with the custom Contact Me client (see portfolio policy: keep the SDK when present). The `vendor/` dir also contains local composer WPCS tooling that is untracked and not gitignored — never `git add -A`; stage migration files explicitly.
