# Authn/Authz Policies and Roles

## Objectives

- Implement session-based authentication.
- Enforce authorization with policies and gates.
- Introduce role model and role-aware access rules.
- Apply ownership rules for project/task CRUD.

## Recommended Order

- Build on top of CRUD concepts from lesson 01.

## Lesson Sequence

1. Install Laravel Breeze with Inertia + Vue.
2. Create roles schema and role assignment workflow.
3. Define and apply policies for project/task ownership and admin actions.
4. Add tests covering allowed and forbidden behavior.

## Run This Lesson

1. Point DDEV docroot to lesson 02:
   - `ddev config --docroot=lessons/02-authn-authz-policies-roles/app/public --project-type=php --auto`
   - `ddev restart`
2. Prepare DB and seed demo users:
   - `ddev exec bash -lc 'cd lessons/02-authn-authz-policies-roles/app && php artisan migrate:fresh --seed'`
3. Visit app:
   - `https://laravel-tutorial.ddev.site`
   - login: `test@example.com` / `password` (member)
   - login: `admin@example.com` / `password` (admin)

## API Surface for This Lesson

- `GET/POST /projects`
- `GET/PATCH/DELETE /projects/{project}`
- `GET/POST /tasks`
- `GET/PATCH/DELETE /tasks/{task}`
- `GET /health`

All project/task routes require authenticated session (`auth` middleware).

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
