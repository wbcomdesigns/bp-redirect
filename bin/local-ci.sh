#!/usr/bin/env bash
# Local CI for WB Login Logout Redirect: the blocking checks of .github/workflows/ci.yml plus
# release consistency. Exit 1 on any failure.
set -uo pipefail
cd "$(dirname "$0")/.."
fail=0
step() { printf '\n== %s\n' "$1"; }
bad() { echo "FAIL: $1"; fail=1; }

step "PHP lint"
while IFS= read -r f; do php -l "$f" >/dev/null || bad "lint $f"; done < <(find . -name '*.php' -not -path './vendor/*' -not -path './libs/*' -not -path './node_modules/*' -not -path './dist/*')

step "Version triangulation"
hv=$(grep -m1 -E '^\s*\*\s*Version:' bp-redirect.php | awk '{print $NF}')
cv=$(grep -m1 "define( 'WBCOM_REDIRECT_VERSION'" bp-redirect.php | grep -oE "[0-9]+\.[0-9]+\.[0-9]+")
rv=$(grep -m1 -i 'Stable tag' readme.txt | tr -d '\r' | awk '{print $NF}')
pv=$(grep -m1 '"version"' package.json | grep -oE "[0-9]+\.[0-9]+\.[0-9]+")
echo "header=$hv const=$cv readme=$rv package=$pv"
[ "$hv" = "$cv" ] && [ "$cv" = "$rv" ] && [ "$rv" = "$pv" ] || bad "version mismatch"

step "Changelog entry"
grep -q "^= $hv" readme.txt || bad "readme.txt has no = $hv = changelog entry"

step "Update SDK present"
[ -f libs/edd-sl-sdk/edd-sl-sdk.php ] && [ -f libs/package-signatures/package-signatures.php ] || bad "update SDK or package-signatures missing from libs/"
grep -q 1_0_4_3 libs/edd-sl-sdk/edd-sl-sdk.php || bad "libs/edd-sl-sdk is not the Wbcom build (1.0.4.3)"

step "WPCS"
if [ -x vendor/bin/phpcs ]; then
	vendor/bin/phpcs -q --standard=.phpcs.xml.dist || bad "phpcs"
else
	echo "skip: vendor/bin/phpcs not installed (composer install)"
fi

[ $fail -eq 0 ] && echo -e "\nALL CHECKS PASSED" || echo -e "\nCHECKS FAILED"
exit $fail
