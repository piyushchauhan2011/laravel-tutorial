# Lesson 03 API Contract

## Prefix

- `/api/v1`

## Required Endpoints (minimum)

- `POST /api/v1/tokens`
- `DELETE /api/v1/tokens/current`
- `GET /api/v1/projects`
- `POST /api/v1/projects`
- `GET /api/v1/projects/{project}`
- `PATCH /api/v1/projects/{project}`
- `DELETE /api/v1/projects/{project}`

## Query Parameters

For list endpoints:

- `page`
- `per_page`
- `sort`
- `direction`
- `filter[status]`

## Envelopes

Use the workspace API standard from `infra/standards/api-contract.md`.
