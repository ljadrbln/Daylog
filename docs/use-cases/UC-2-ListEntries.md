# UC-2 — List Entries

## Intent
Display a paginated list of diary entries in a single-user environment, with optional filters and sorting.

## Preconditions
- Application is running.
- The system may contain zero or more entries.

## Main Success Scenario
1. The system receives parameters: `page`, `perPage`, `sort` (field and direction), and optional filters (`dateFrom`, `dateTo`, `date`, `query` for title/body).
2. The system validates and normalizes inputs (date formats, perPage bounds).
3. The system retrieves matching entries from storage.
4. The system returns a result page with: `items`, `total`, `page`, `perPage`, `pagesCount`.

## Alternative / Error Flows
- **AF-1**: Invalid date format → validation error `DATE_INVALID_FORMAT`.
- **AF-2**: `perPage` below minimum or above maximum → clamped to allowed bounds.
- **AF-3**: No matching results → return an empty list with valid pagination metadata.

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
- **AC-6 (invalid date format)**: A non-`YYYY-MM-DD` date input causes `DATE_INVALID_FORMAT`.
- **AC-7 (single date)**: With `date=YYYY-MM-DD`, only entries with an exact logical date match are returned.
- **AC-8 (stable order)**: When sort keys are equal, a stable secondary order by `createdAt DESC` is applied.
