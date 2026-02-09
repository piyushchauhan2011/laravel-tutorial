#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
LESSONS_DIR="$ROOT_DIR/lessons"
DRY_RUN="false"
SKIP_COMPOSER="false"
REGEN_DOCS="true"

# shellcheck source=./lib-lessons.sh
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lib-lessons.sh"

usage() {
  cat <<TXT
Usage: $(basename "$0") [options]

Options:
  --dry-run         Print commands without executing
  --skip-composer   Do not run composer create-project (create placeholder app folders)
  --no-docs         Skip lesson docs regeneration
  -h, --help        Show this help message
TXT
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run)
      DRY_RUN="true"
      ;;
    --skip-composer)
      SKIP_COMPOSER="true"
      ;;
    --no-docs)
      REGEN_DOCS="false"
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "Unknown option: $1" >&2
      usage
      exit 1
      ;;
  esac
  shift
done

run_cmd() {
  if [[ "$DRY_RUN" == "true" ]]; then
    echo "[DRY-RUN] $*"
  else
    "$@"
  fi
}

dir_is_empty() {
  local dir="$1"
  [[ -d "$dir" ]] || return 1
  [[ -z "$(find "$dir" -mindepth 1 -maxdepth 1 2>/dev/null)" ]]
}

placeholder_is_only_file() {
  local app_dir="$1"
  local count
  count="$(find "$app_dir" -mindepth 1 -maxdepth 1 ! -name '.app-not-generated' | wc -l | tr -d ' ')"
  [[ "$count" == "0" ]]
}

update_env_for_postgres() {
  local app_dir="$1"
  local db_name="$2"
  local env_file="$app_dir/.env"

  [[ -f "$env_file" ]] || return 0

  if rg -q '^[# ]*DB_CONNECTION=' "$env_file"; then
    sed -i.bak 's|^[# ]*DB_CONNECTION=.*|DB_CONNECTION=pgsql|' "$env_file"
  else
    echo "DB_CONNECTION=pgsql" >> "$env_file"
  fi

  if rg -q '^[# ]*DB_HOST=' "$env_file"; then
    sed -i.bak 's|^[# ]*DB_HOST=.*|DB_HOST=db|' "$env_file"
  else
    echo "DB_HOST=db" >> "$env_file"
  fi

  if rg -q '^[# ]*DB_PORT=' "$env_file"; then
    sed -i.bak 's|^[# ]*DB_PORT=.*|DB_PORT=5432|' "$env_file"
  else
    echo "DB_PORT=5432" >> "$env_file"
  fi

  if rg -q '^[# ]*DB_DATABASE=' "$env_file"; then
    sed -i.bak "s|^[# ]*DB_DATABASE=.*|DB_DATABASE=${db_name}|" "$env_file"
  else
    echo "DB_DATABASE=${db_name}" >> "$env_file"
  fi

  if rg -q '^[# ]*DB_USERNAME=' "$env_file"; then
    sed -i.bak 's|^[# ]*DB_USERNAME=.*|DB_USERNAME=db|' "$env_file"
  else
    echo "DB_USERNAME=db" >> "$env_file"
  fi

  if rg -q '^[# ]*DB_PASSWORD=' "$env_file"; then
    sed -i.bak 's|^[# ]*DB_PASSWORD=.*|DB_PASSWORD=db|' "$env_file"
  else
    echo "DB_PASSWORD=db" >> "$env_file"
  fi

  rm -f "$env_file.bak"
}

echo "Initializing lesson apps under: $LESSONS_DIR"

if [[ "$REGEN_DOCS" == "true" ]]; then
  if [[ "$DRY_RUN" == "true" ]]; then
    echo "[DRY-RUN] bash infra/scripts/generate-lesson-docs.sh"
  else
    bash "$ROOT_DIR/infra/scripts/generate-lesson-docs.sh"
  fi
fi

for lesson in "${LESSON_SPECS[@]}"; do
  slug="${lesson%%|*}"
  lesson_dir="$LESSONS_DIR/$slug"
  app_dir="$lesson_dir/app"
  db_name="$(lesson_db_name "$slug")"
  placeholder_marker="$app_dir/.app-not-generated"

  mkdir -p "$lesson_dir"

  if [[ ! -d "$app_dir" ]]; then
    if [[ "$SKIP_COMPOSER" == "true" ]]; then
      echo "[$slug] Creating placeholder app directory (composer skipped)."
      run_cmd mkdir -p "$app_dir"
      run_cmd touch "$app_dir/.app-not-generated"
    else
      echo "[$slug] Creating Laravel app via Composer..."
      if [[ "$DRY_RUN" == "true" ]]; then
        echo "[DRY-RUN] composer create-project laravel/laravel '$app_dir' --no-interaction --prefer-dist"
      else
        composer create-project laravel/laravel "$app_dir" --no-interaction --prefer-dist
      fi
    fi
  elif [[ -f "$placeholder_marker" && "$SKIP_COMPOSER" != "true" ]]; then
    if placeholder_is_only_file "$app_dir"; then
      echo "[$slug] Replacing placeholder with real Laravel app..."
      if [[ "$DRY_RUN" == "true" ]]; then
        echo "[DRY-RUN] rm -f '$placeholder_marker'"
        echo "[DRY-RUN] composer create-project laravel/laravel '$app_dir' --no-interaction --prefer-dist"
      else
        rm -f "$placeholder_marker"
        composer create-project laravel/laravel "$app_dir" --no-interaction --prefer-dist
      fi
    else
      echo "[$slug] Placeholder marker exists but app directory has additional files; skipping to avoid destructive overwrite."
    fi
  elif [[ "$SKIP_COMPOSER" != "true" ]] && dir_is_empty "$app_dir"; then
    echo "[$slug] App directory is empty; creating Laravel app via Composer..."
    if [[ "$DRY_RUN" == "true" ]]; then
      echo "[DRY-RUN] composer create-project laravel/laravel '$app_dir' --no-interaction --prefer-dist"
    else
      composer create-project laravel/laravel "$app_dir" --no-interaction --prefer-dist
    fi
  else
    echo "[$slug] App directory already exists, skipping create-project."
  fi

  if [[ "$DRY_RUN" != "true" && "$SKIP_COMPOSER" != "true" && -f "$app_dir/.env" ]]; then
    update_env_for_postgres "$app_dir" "$db_name"
  fi

  echo "[$slug] Done"
done

echo "Lesson app initialization completed."
