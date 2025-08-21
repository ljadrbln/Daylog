# UC-2 — List Entries

## Intent
Display a paginated list of diary entries in a single-user environment, with optional filters and sorting.

## Preconditions
- Application is running.
- The system may contain zero or more entries.

## Parameters & Limits
- `page`: integer ≥ 1. Defaults to 1.
- `perPage`: integer. Clamped to range 1..100. Defaults to 20.
- `sort`: field ∈ {`date`, `createdAt`, `updatedAt`} × direction ∈ {`ASC`, `DESC`}. Defaults to `date DESC`. Invalid values → fallback to `date DESC`.
- `date`: string. See BR-6 (YYYY-MM-DD, valid calendar date).
- `dateFrom` / `dateTo`: strings. See BR-6. Inclusive range.
- `query`: string, 0..30 chars (after trimming). Empty string means “no filter”. Case-insensitive substring match in `title` and `body`.

## Main Success Scenario
1. The system receives parameters: `page`, `perPage`, `sort` (field and direction), and optional filters (`dateFrom`, `dateTo`, `date`, `query` for title/body).
2. The system validates and normalizes inputs (date formats, perPage bounds).
3. The system retrieves matching entries from storage.
4. The system returns a result page with: `items`, `total`, `page`, `perPage`, `pagesCount`.

## Alternative / Error Flows
- **AF-1**: Invalid date input (format or calendar) → validation error `DATE_INVALID`.
- **AF-2**: `perPage` below minimum or above maximum → clamped to allowed bounds.
- **AF-3**: No matching results → return an empty list with valid pagination metadata.
- **AF-4**: `query` longer than 30 chars (after trimming) → validation error `QUERY_TOO_LONG`.
- **AF-5**: `dateFrom > dateTo` → validation error `DATE_RANGE_INVALID`.

## Postconditions
- The system returns a consistent and stable view of entries.
- Sorting and pagination are deterministic.

## Business Rules (referencing globals)
- BR-3 Input trimming applies to string filters.
- BR-4 Timestamps consistency (sorting by `createdAt`/`updatedAt` must respect invariants).
- BR-6 Entry date is used for filtering by logical entry date (distinct from timestamps).

## Acceptance Criteria
- **AC-1 (happy path)**: With no filters, the first page is returned, sorted by `date DESC` by default, including pagination metadata.
- **AC-2 (date range)**: With `dateFrom`/`dateTo`, only entries whose `date` is within the inclusive range are returned.
- **AC-3 (full-text query)**: With `query`, entries are returned if either `title` or `body` contains the substring, case-insensitive.
- **AC-4 (pagination bounds)**: If `perPage` is outside limits, it is clamped to allowed values. Empty pages are valid.
- **AC-5 (sorting)**: Sorting is supported by `date`, `createdAt`, `updatedAt` with `ASC|DESC`. Invalid values fall back to `date DESC`.
- **AC-6 (invalid date input)**: A non-`YYYY-MM-DD` date (or non-real calendar date) causes `DATE_INVALID`.
- **AC-7 (single date)**: With `date=YYYY-MM-DD`, only entries with an exact logical date match are returned.
- **AC-8 (stable order)**: When sort keys are equal, a stable secondary order by `createdAt DESC` is applied.
- **AC-9 (query length)**: Given `query` longer than 30 chars (after trimming), validation fails with `QUERY_TOO_LONG`.
- **AC-10 (date range order)**: Given `dateFrom > dateTo`, validation fails with `DATE_RANGE_INVALID`.