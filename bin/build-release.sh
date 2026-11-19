#!/usr/bin/env bash
# Build dist/bp-redirect-<version>.zip: local CI, then grunt dist (assets, POT, copy minus
# .distignore, zip), then check the zip for dev files.
set -euo pipefail
cd "$(dirname "$0")/.."
slug=bp-redirect
bin/local-ci.sh
[ -d node_modules ] || npm ci
npx grunt dist
version=$(grep -m1 '"version"' package.json | grep -oE "[0-9]+\.[0-9]+\.[0-9]+")
zip="dist/$slug-$version.zip"
leaks=$(unzip -l "$zip" | grep -iE "$slug/(audit|bin|docs|tests|node_modules)/|CLAUDE|QA-TESTING|\.md$|\.map$|gruntfile|package(-lock)?\.json|composer\.(json|lock)|\.distignore" || true)
[ -z "$leaks" ] || { echo "Zip contains dev files:"; echo "$leaks"; exit 1; }
for f in libs/edd-sl-sdk/edd-sl-sdk.php libs/edd-sl-sdk/src/Versions.php libs/edd-sl-sdk/src/Requests/API.php libs/package-signatures/package-signatures.php libs/package-signatures/src/class-guard.php libs/package-signatures/src/class-verifier.php; do
	unzip -l "$zip" | grep -q "$slug/$f" || { echo "Zip is missing $f"; exit 1; }
done
unzip -l "$zip" | grep -qE "$slug/vendor/" && { echo "Zip ships vendor/ dev tooling"; exit 1; }
echo "Built $zip"
