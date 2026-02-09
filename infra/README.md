# Shared Infrastructure

This workspace uses one shared PostgreSQL service and one database per lesson app.

## DDEV Strategy

- Root project runs DDEV for this repository.
- PostgreSQL is provided by DDEV's database service.
- Each lesson app gets its own `DB_DATABASE` value.

## Database Naming

Databases are prefixed with `lesson_` and mirror lesson slugs.

Examples:

- `lesson_00_laravel_cli_foundations`
- `lesson_03_api_routes_sanctum_versioning`
- `lesson_11_capstone_production_style_app`

## Scripts

- `infra/scripts/verify-toolchain.sh`: validates local toolchain
- `infra/scripts/init-all-lessons.sh`: generates Laravel apps + docs
- `infra/scripts/provision-databases.sh`: creates lesson databases in Postgres

## Environment Mapping

Generated apps are configured with:

- `DB_CONNECTION=pgsql`
- `DB_HOST=db`
- `DB_PORT=5432`
- `DB_DATABASE=<lesson_database_name>`
- `DB_USERNAME=db`
- `DB_PASSWORD=db`

If you run Laravel commands outside DDEV containers, use host networking values
like `DB_HOST=127.0.0.1` and `DB_PORT=55432`.
