#!/usr/bin/env bash
# Build dist/bp-redirect-<version>.zip: local CI, then grunt dist (assets, POT, copy minus
# .distignore, zip), then check the zip for dev files.
set -euo pipefail
cd "$(dirname "$0")/.."
slug=bp-redirect
# The update SDK is a git submodule; a fresh clone or `git archive` leaves it empty.
git submodule update --init --recursive
bin/local-ci.sh
[ -d node_modules ] || npm ci
npx grunt dist
version=$(grep -m1 '"version"' package.json | grep -oE "[0-9]+\.[0-9]+\.[0-9]+")
zip="dist/$slug-$version.zip"
leaks=$(unzip -l "$zip" | grep -iE "$slug/(audit|bin|docs|tests|node_modules)/|CLAUDE|QA-TESTING|\.md$|\.map$|gruntfile|package(-lock)?\.json|composer\.(json|lock)|\.distignore" || true)
[ -z "$leaks" ] || { echo "Zip contains dev files:"; echo "$leaks"; exit 1; }
unzip -l "$zip" | grep "$slug/vendor/easy-digital-downloads/edd-sl-sdk/edd-sl-sdk.php" >/dev/null || { echo "Zip has no update SDK"; exit 1; }
echo "Built $zip"
