#!/usr/bin/env bash
# Invoked only after local verification. Do not edit application source on the VPS.
set -euo pipefail
expected_commit=${1:?}
release_root=${2:?}
php_bin=${3:?}
web_user=${4:?}
worker_service=${5:?}
artifact=${6:?}
artifact_hash=${7:?}
[[ "$expected_commit" =~ ^[a-f0-9]{40}$ && "$artifact_hash" =~ ^[a-f0-9]{64}$ ]] || exit 1
cd "$release_root"
exec 9>.git/deploy.lock
flock -n 9 || { echo 'Another deployment is running'; exit 1; }
[[ "$(git branch --show-current)" == main ]] || { echo 'VPS must be on main'; exit 1; }
[[ -z "$(git status --porcelain)" ]] || { echo 'VPS contains uncommitted changes; reconcile and back them up before deployment'; git status --short; exit 1; }
printf '%s  %s\n' "$artifact_hash" "$artifact" | sha256sum --check --status
previous_commit=$(git rev-parse HEAD)
git fetch origin "$expected_commit"
git merge-base --is-ancestor "$previous_commit" "$expected_commit" || { echo 'Refusing non-fast-forward deployment'; exit 1; }
# Dependency changes need a reviewed dependency rollout, not an unplanned install.
if ! git diff --quiet "$previous_commit" "$expected_commit" -- composer.lock; then
    echo 'composer.lock changed: prepare production vendor dependencies before deploying'; exit 1
fi
mkdir -p .git/deployments
printf '%s\n' "$previous_commit" > ".git/deployments/$expected_commit.previous"
tar -czf ".git/deployments/$expected_commit.build-before.tar.gz" -C public build
# Pull the exact SHA that passed local tests, even if main advances concurrently.
git pull --ff-only origin "$expected_commit"
[[ "$(git rev-parse HEAD)" == "$expected_commit" ]] || exit 1
tar -xzf "$artifact" -C public
chown -R "$web_user:$web_user" public/build
runuser -u "$web_user" -- "$php_bin" artisan migrate --force
runuser -u "$web_user" -- "$php_bin" artisan optimize
runuser -u "$web_user" -- "$php_bin" artisan filament:optimize
if [[ -f "deploy/$worker_service.service" ]]; then
    install -m 644 "deploy/$worker_service.service" "/etc/systemd/system/$worker_service.service"
    systemctl daemon-reload
    systemctl enable "$worker_service.service"
    systemctl restart "$worker_service.service"
    systemctl is-active --quiet "$worker_service.service"
fi
[[ -z "$(git status --porcelain)" ]] || { echo 'Unexpected tracked changes after deployment'; git status --short; exit 1; }
printf 'commit=%s\nprevious=%s\nbuild_sha256=%s\ncompleted_utc=%s\n' "$expected_commit" "$previous_commit" "$artifact_hash" "$(date -u +%FT%TZ)" > ".git/deployments/$expected_commit.receipt"
echo "VPS verified commit: $(git rev-parse HEAD)"
