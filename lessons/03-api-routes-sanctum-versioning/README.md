# API Routes, Sanctum, Versioning

## Objectives

- Design versioned Laravel APIs.
- Secure APIs with Sanctum token authentication.
- Enforce unified request validation and response envelopes.

## Recommended Order

- Complete after auth lesson 02.

## Lesson Sequence

1. Create /api/v1 route group and versioned controllers.
2. Implement token issue/revoke endpoints with Sanctum.
3. Return resource responses in { data, meta } envelope.
4. Add global API exception handling with { error: { code, message, details } } format.

## Run This Lesson

1. Point DDEV to lesson 03:
   - `ddev config --docroot=lessons/03-api-routes-sanctum-versioning/app/public --project-type=php --auto`
   - `ddev restart`
2. Migrate and seed:
   - `ddev exec bash -lc 'cd lessons/03-api-routes-sanctum-versioning/app && php artisan migrate:fresh --seed'`
3. API base URL:
   - `https://laravel-tutorial.ddev.site/api/v1`

## Endpoints Implemented

- `POST /api/v1/tokens`
- `DELETE /api/v1/tokens/current`
- `GET /api/v1/projects`
- `POST /api/v1/projects`
- `GET /api/v1/projects/{project}`
- `PATCH /api/v1/projects/{project}`
- `DELETE /api/v1/projects/{project}`

## Quick Token Flow

1. Create token:
   - `POST /api/v1/tokens` with `email`, `password`, `device_name`
2. Use the returned token:
   - `Authorization: Bearer <access_token>`
3. Revoke current token:
   - `DELETE /api/v1/tokens/current`

### Curl Commands (Tested)

```bash
BASE_URL="https://laravel-tutorial.ddev.site"

# 1) issue token
curl -i -s -X POST "$BASE_URL/api/v1/tokens" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"password","device_name":"local-cli"}'

# 2) capture token (requires jq)
TOKEN=$(curl -s -X POST "$BASE_URL/api/v1/tokens" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"password","device_name":"local-cli"}' \
  | jq -r '.data.access_token // empty')

# 3) call protected endpoint
curl -i -s "$BASE_URL/api/v1/projects" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

# 4) revoke current token
curl -i -s -X DELETE "$BASE_URL/api/v1/tokens/current" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

## Troubleshooting

- If `/api/v1/tokens` returns 404 and the error trace references lesson 02 paths,
  your DDEV docroot is still pointing at lesson 02.
- Verify active docroot:
  - `ddev describe`
- Switch to lesson 03 docroot:
  - `ddev config --docroot=lessons/03-api-routes-sanctum-versioning/app/public --project-type=php --auto`
  - `ddev restart`
- Ensure seed user exists before token issue:
  - `ddev exec bash -lc 'cd lessons/03-api-routes-sanctum-versioning/app && php artisan migrate:fresh --seed'`

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
- Keep API routes under /api/v1/* and use the shared response envelope contract.
