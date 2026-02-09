# Onboarding

This guide helps you run lessons reliably in this multi-app Laravel workspace.

## 1. Prerequisites

- PHP and Composer installed
- Docker running
- DDEV installed

Verify:

```bash
bash infra/scripts/verify-toolchain.sh
```

## 2. Bootstrap Workspace

```bash
ddev start
bash infra/scripts/init-all-lessons.sh
bash infra/scripts/provision-databases.sh
```

## 3. Important Runtime Rule

This repository contains multiple Laravel apps (`lessons/*/app`), but DDEV serves one docroot at a time.

You must point DDEV `docroot` to the lesson you want to open in browser.

## 4. Run Lesson 00 (Recommended First)

Set lesson 00 as docroot:

```bash
ddev config --docroot=lessons/00-laravel-cli-foundations/app/public --project-type=php --auto
ddev restart
```

Open:

- `http://laravel-tutorial.ddev.site/`
- `http://laravel-tutorial.ddev.site/health`
- `http://laravel-tutorial.ddev.site/notes`

Run lesson commands:

```bash
ddev exec bash -lc 'cd lessons/00-laravel-cli-foundations/app && php artisan about'
ddev exec bash -lc 'cd lessons/00-laravel-cli-foundations/app && php artisan route:list --except-vendor'
ddev exec bash -lc 'cd lessons/00-laravel-cli-foundations/app && php artisan test'
```

## 5. Switch To Another Lesson

Example for lesson 01:

```bash
ddev config --docroot=lessons/01-crud-and-data-modeling/app/public --project-type=php --auto
ddev restart
```

Then browse `http://laravel-tutorial.ddev.site/`.

## 6. Troubleshooting

- `403 Forbidden` at root:
  - DDEV docroot points to a folder without `index.php`.
- nginx `404` for lesson path URL:
  - You are trying to access a nested app path while docroot is not set to that app.
- DB connection errors in app commands:
  - Run commands through `ddev exec`.
  - Ensure `.env` has `DB_HOST=db` and `DB_PORT=5432`.

## 7. Optional Git Workflow

```bash
git status
git add .
git commit -m "Describe your lesson progress"
```
