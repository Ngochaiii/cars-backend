#!/usr/bin/env bash
# Run from the local checkout. Source always travels through Git, never rsync.
set -euo pipefail
cd "$(git rev-parse --show-toplevel)"
if [[ "${1:-}" != '--go' ]]; then
    echo 'Usage: DEPLOY_HOST=user@host ./scripts/deploy.sh --go'
    echo 'Requires a clean main branch. Runs local tests/build, pushes, then pulls the tested commit on the VPS.'
    exit 0
fi
: "${DEPLOY_HOST:?Set DEPLOY_HOST}"
DEPLOY_ROOT=${DEPLOY_ROOT:-/www/wwwroot/main}
DEPLOY_PHP=${DEPLOY_PHP:-/www/server/php/83/bin/php}
DEPLOY_USER=${DEPLOY_USER:-www}
DEPLOY_SERVICE=${DEPLOY_SERVICE:-cars-articles}
DEPLOY_URL=${DEPLOY_URL:-https://vinfast-bacgiang.com}
for value in "$DEPLOY_ROOT" "$DEPLOY_PHP" "$DEPLOY_USER" "$DEPLOY_SERVICE"; do
    [[ "$value" =~ ^[a-zA-Z0-9_./-]+$ ]] || { echo 'Unsupported remote path or service name'; exit 1; }
done
[[ "$DEPLOY_ROOT" != '/' && "$DEPLOY_ROOT" == /* ]] || exit 1
[[ "$(git branch --show-current)" == main ]] || { echo 'Deploy only from main'; exit 1; }
[[ -z "$(git status --porcelain)" ]] || { echo 'Commit or preserve local changes before deploying'; exit 1; }
git fetch origin main
expected_commit=$(git rev-parse HEAD)
git merge-base --is-ancestor origin/main "$expected_commit" || { echo 'Local branch is behind or diverged from origin/main'; exit 1; }
echo "Testing local commit $expected_commit"
php artisan config:clear
php artisan test --compact
npm run build
git diff --check
[[ "$(git rev-parse HEAD)" == "$expected_commit" && -z "$(git status --porcelain)" ]] || { echo 'Checkout changed during verification'; exit 1; }
artifact=$(mktemp /tmp/cars-build.XXXXXX)
trap 'rm -f "$artifact"' EXIT
COPYFILE_DISABLE=1 tar -czf "$artifact" -C public build
artifact_hash=$(shasum -a 256 "$artifact" | awk '{print $1}')
remote_artifact="/tmp/cars-build-$expected_commit.tar.gz"
git push origin "$expected_commit:refs/heads/main"
scp "$artifact" "$DEPLOY_HOST:$remote_artifact"
ssh "$DEPLOY_HOST" "bash -s -- '$expected_commit' '$DEPLOY_ROOT' '$DEPLOY_PHP' '$DEPLOY_USER' '$DEPLOY_SERVICE' '$remote_artifact' '$artifact_hash'" < scripts/deploy-remote.sh
curl --fail --silent --show-error --output /dev/null --write-out 'Website HTTP %{http_code}\n' "$DEPLOY_URL/"
echo "Deployed tested commit $expected_commit"
