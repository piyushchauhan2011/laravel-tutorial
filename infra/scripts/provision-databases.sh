#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DRY_RUN="false"

# shellcheck source=./lib-lessons.sh
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lib-lessons.sh"

if [[ "${1:-}" == "--dry-run" ]]; then
  DRY_RUN="true"
fi

db_exists() {
  local db_name="$1"
  ddev exec psql -U db -d postgres -tAc "SELECT 1 FROM pg_database WHERE datname='${db_name}'" | tr -d '[:space:]'
}

echo "Provisioning PostgreSQL databases for lesson apps in: $ROOT_DIR"

for lesson in "${LESSON_SPECS[@]}"; do
  slug="${lesson%%|*}"
  db_name="$(lesson_db_name "$slug")"
  echo "Ensuring DB exists: $db_name"
  if [[ "$DRY_RUN" == "true" ]]; then
    echo "[DRY-RUN] ddev exec psql -U db -d postgres -tAc \"SELECT 1 FROM pg_database WHERE datname='${db_name}'\""
    echo "[DRY-RUN] ddev exec createdb -U db ${db_name} (if missing)"
  else
    if [[ "$(db_exists "$db_name")" == "1" ]]; then
      echo "  -> already exists"
    else
      ddev exec createdb -U db "$db_name"
      echo "  -> created"
    fi
  fi
done

echo "Database provisioning completed."
