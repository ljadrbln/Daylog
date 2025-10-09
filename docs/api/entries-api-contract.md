# Entries API Contract — Derived from Functional Test Log

> **Scope:** UC‑1 AddEntry, UC‑2 ListEntries, UC‑3 GetEntry, UC‑4 DeleteEntry, UC‑5 UpdateEntry  

---

## 0. Envelope (observed)

Every endpoint returns JSON with these fields observed in the log:

- `success: boolean`
- `status: number`
- `data: object` (present **only when** `success=true`)
- `code: string` (present **only when** `success=false`)

---

## 1) UC‑1 — AddEntry

### Success — 200
```json
{
  "success": true,
  "data": {
    "id": "829ad7de-147d-4f6d-bb6b-f8be26aa4108",
    "title": "Valid title",
    "body": "Valid body",
    "date": "2025-02-12",
    "createdAt": "2025-10-09T13:01:57+00:00",
    "updatedAt": "2025-10-09T13:01:57+00:00"
  },
  "status": 200
}
```

### Missing/required fields — 400
Representative cases (each returns one `code`):

- `TITLE_REQUIRED`
- `BODY_REQUIRED`
- `DATE_REQUIRED`

Example:
```json
{
  "success": false,
  "status": 400,
  "code": "TITLE_REQUIRED"
}
```

### Validation errors — 422
Representative cases:

- `TITLE_TOO_LONG`
- `BODY_TOO_LONG`
- `DATE_INVALID`
- `TITLE_REQUIRED` (when empty/whitespace after normalization)

Example:
```json
{
  "success": false,
  "status": 422,
  "code": "TITLE_REQUIRED"
}
```

---

## 3) UC‑2 — ListEntries

### Success — 200
The response contains `items[]` and pagination fields.

```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": "426cce2d-59c8-4858-abe8-4f4f2256ef44",
        "title": "Valid title",
        "body": "Valid body",
        "date": "2025-02-14",
        "createdAt": "2025-02-12T10:00:02+00:00",
        "updatedAt": "2025-02-12T10:00:02+00:00"
      },
      {
        "id": "7588ed92-45a6-4298-8117-67b93257375e",
        "title": "Valid title",
        "body": "Valid body",
        "date": "2025-02-13",
        "createdAt": "2025-02-12T10:00:01+00:00",
        "updatedAt": "2025-02-12T10:00:01+00:00"
      },
      {
        "id": "ecdf598b-eec4-44c1-b2a2-226938270e6e",
        "title": "Valid title",
        "body": "Valid body",
        "date": "2025-02-12",
        "createdAt": "2025-02-12T10:00:00+00:00",
        "updatedAt": "2025-02-12T10:00:00+00:00"
      }
    ],
    "page": 1,
    "perPage": 10,
    "total": 3,
    "pagesCount": 1
  },
  "status": 200
}
```

### Empty page — 200
```json
{
  "success": true,
  "data": {
    "items": [],
    "page": 10,
    "perPage": 2,
    "total": 5,
    "pagesCount": 3
  },
  "status": 200
}
```

### Validation errors — 422
Representative cases:

- `DATE_INVALID`
- `DATE_RANGE_INVALID`
- `QUERY_TOO_LONG`

Example:
```json
{
  "success": false,
  "status": 422,
  "code": "DATE_INVALID"
}
```

### Type/shape errors — 400
Representative cases (parameters sent as arrays or wrong primitive type):

- `PAGE_MUST_BE_NUMERIC`
- `PER_PAGE_MUST_BE_NUMERIC`
- `SORT_FIELD_MUST_BE_STRING`
- `DIRECTION_MUST_BE_STRING`
- `DATE_FROM_MUST_BE_STRING`
- `DATE_TO_MUST_BE_STRING`
- `DATE_MUST_BE_STRING`
- `QUERY_MUST_BE_STRING`

Example:
```json
{
  "success": false,
  "status": 400,
  "code": "PAGE_MUST_BE_NUMERIC"
}
```

---

## 3) UC‑3 — GetEntry

### Success — 200
```json
{
  "success": true,
  "data": {
    "id": "3d85af08-dfa5-48b3-bf04-b519852c72a2",
    "title": "Valid title",
    "body": "Valid body",
    "date": "2025-02-12",
    "createdAt": "2025-10-09T12:01:57+00:00",
    "updatedAt": "2025-10-09T12:01:57+00:00"
  },
  "status": 200
}
```

### Not found — 404
```json
{
  "success": false,
  "status": 404,
  "code": "ENTRY_NOT_FOUND"
}
```

### Invalid id — 422
```json
{
  "success": false,
  "status": 422,
  "code": "ID_INVALID"
}
```

---

## 4) UC‑4 — DeleteEntry

### Success — 200
Returns the deleted entry **object** in `data`:
```json
{
  "success": true,
  "data": {
    "id": "d914fa57-db05-47e0-92ee-9f56386ccda5",
    "title": "Valid title",
    "body": "Valid body",
    "date": "2025-02-12",
    "createdAt": "2025-10-09T12:01:57+00:00",
    "updatedAt": "2025-10-09T12:01:57+00:00"
  },
  "status": 200
}
```

### Not found — 404
```json
{
  "success": false,
  "status": 404,
  "code": "ENTRY_NOT_FOUND"
}
```

### Invalid id — 422
```json
{
  "success": false,
  "status": 422,
  "code": "ID_INVALID"
}
```

---

## 5) UC‑5 — UpdateEntry

### Success — 200
```json
{
  "success": true,
  "data": {
    "id": "230cd464-2c3b-422e-bff0-705657ba72c4",
    "title": "Updated title",
    "body": "Valid body",
    "date": "2025-02-12",
    "createdAt": "2025-10-09T12:01:59+00:00",
    "updatedAt": "2025-10-09T13:02:00+00:00"
  },
  "status": 200
}
```

### Validation / business errors — 422
Representative cases:

- `ID_INVALID`
- `TITLE_REQUIRED`
- `TITLE_TOO_LONG`
- `BODY_REQUIRED`
- `BODY_TOO_LONG`
- `DATE_INVALID`
- `NO_FIELDS_TO_UPDATE`
- `NO_CHANGES_APPLIED`

Example:
```json
{
  "success": false,
  "status": 422,
  "code": "NO_FIELDS_TO_UPDATE"
}
```

### Not found — 404
```json
{
  "success": false,
  "status": 404,
  "code": "ENTRY_NOT_FOUND"
}
```
