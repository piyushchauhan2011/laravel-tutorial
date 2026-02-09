#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

check_cmd() {
  local cmd="$1"
  if ! command -v "$cmd" >/dev/null 2>&1; then
    echo "[FAIL] Missing required command: $cmd"
    return 1
  fi
  echo "[OK] Found command: $cmd"
}

echo "Verifying toolchain in: $ROOT_DIR"

check_cmd php
check_cmd composer
check_cmd docker
check_cmd ddev

echo
php -v | sed -n '1p'
composer --version

docker --version || true

echo
if ddev version >/dev/null 2>&1; then
  ddev version | sed -n '1,6p'
else
  echo "[WARN] ddev command exists but could not talk to Docker. Start Docker Desktop and rerun."
fi

echo
if [[ -f "$ROOT_DIR/.ddev/config.yaml" ]]; then
  echo "[OK] Found DDEV config: $ROOT_DIR/.ddev/config.yaml"
else
  echo "[WARN] Missing DDEV config at root"
fi

echo "Toolchain verification completed."
