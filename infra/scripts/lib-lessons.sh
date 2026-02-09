#!/usr/bin/env bash

# Shared lesson registry and helpers for all workspace scripts.

LESSON_SPECS=(
  "00-laravel-cli-foundations|Laravel CLI Foundations"
  "01-crud-and-data-modeling|CRUD and Data Modeling"
  "02-authn-authz-policies-roles|Authn/Authz Policies and Roles"
  "03-api-routes-sanctum-versioning|API Routes, Sanctum, Versioning"
  "04-background-jobs-queues-workers|Background Jobs, Queues, Workers"
  "05-scheduler-and-custom-artisan-tasks|Scheduler and Custom Artisan Tasks"
  "06-postgres-ctes-analytics|Postgres CTEs and Analytics"
  "07-vue-inertia-advanced-ui|Vue + Inertia Advanced UI"
  "08-filament-admin-panel|Filament Admin Panel"
  "09-testing-unit-integration-e2e|Testing: Unit, Integration, E2E"
  "10-deployment-frankenphp-vps|Deployment with FrankenPHP on VPS"
  "11-capstone-production-style-app|Capstone Production-style App"
)

lesson_db_name() {
  local slug="$1"
  printf 'lesson_%s\n' "${slug//-/_}"
}

lesson_is_api_focused() {
  local slug="$1"
  [[ "$slug" == "03-api-routes-sanctum-versioning" || "$slug" == "11-capstone-production-style-app" ]]
}
