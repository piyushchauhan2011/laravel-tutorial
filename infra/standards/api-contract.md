# API Contract Standard

## Route Namespace

All API routes in this curriculum should be mounted under:

- `/api/v1/*`

## Authentication

- Use Laravel Sanctum bearer tokens for protected endpoints.
- Public auth endpoints (issue/revoke) must validate payloads and return consistent envelopes.

## Response Envelopes

Success:

```json
{
  "data": {},
  "meta": {}
}
```

Error:

```json
{
  "error": {
    "code": "string_code",
    "message": "Human readable",
    "details": {}
  }
}
```

## Error Codes

Use stable string codes, for example:

- `validation_failed`
- `unauthenticated`
- `forbidden`
- `resource_not_found`
- `conflict`
- `server_error`
