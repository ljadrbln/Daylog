# UC-5 — Update Entry

## Intent
Update an existing diary entry’s mutable fields (`title`, `body`, `date`) by id.

## Preconditions
- Application is running.
- The system will receive `id` (UUID v4) and at least one of: `title`, `body`, `date`.

## Parameters & Limits
- `id`: string (UUID v4). Must reference an existing entry.
- `title`: string (optional). See ENTRY-BR-1 (length after trimming) and BR-1 (trimming).
- `body`: string (optional). See ENTRY-BR-2 (length after trimming) and BR-1 (trimming).
- `date`: string (optional). See BR-2 (YYYY-MM-DD, valid calendar date).
- At least one of `title`, `body`, `date` MUST be provided.

## Main Success Scenario
1. The system receives `id` and a subset of fields (`title`, `body`, `date`).
2. The system validates inputs according to global business rules (BR-1..BR-2, ENTRY-BR-1..ENTRY-BR-2).
3. The system loads the Entry by `id`.
4. The system applies only provided fields (partial update).
5. The system persists the updated Entry and refreshes `updatedAt` (only if at least one field changed).
6. The system returns the updated EntryId (or Presentation payload per current project standard).

## Alternative / Error Flows
- **AF-1**: Missing `id` → `ID_REQUIRED`.
- **AF-2**: Invalid `id` (not UUID v4) → `ID_INVALID`.
- **AF-3**: Entry not found by `id` → `ENTRY_NOT_FOUND`.
- **AF-4**: No fields to update (only `id` provided) → `NO_FIELDS_TO_UPDATE`.
- **AF-5**: Empty `title` after trimming → `TITLE_REQUIRED`.
- **AF-6**: `title` exceeds limit → `TITLE_TOO_LONG`.
- **AF-7**: Empty `body` after trimming → `BODY_REQUIRED`.
- **AF-8**: `body` exceeds limit → `BODY_TOO_LONG`.
- **AF-9**: Invalid `date` input (not strict `YYYY-MM-DD` or not a real calendar date) → `DATE_INVALID`.
- **AF-10**: No effective changes (provided values equal current values) → `NO_CHANGES_APPLIED` (informational, non-error).

## Postconditions
- On success with effective changes: the Entry exists with updated fields and a refreshed `updatedAt`; `createdAt` remains unchanged.
- On success without effective changes: the Entry remains unchanged; `updatedAt` is not modified and the system returns `NO_CHANGES_APPLIED`.
- On failure: the Entry remains unchanged.

## Business Rules (referencing globals)
- ENTRY-BR-1 Title length (1..200) after trimming.
- ENTRY-BR-2 Body length (1..50000).
- BR-1 Trimming.
- BR-2 Timestamps consistency & monotonicity.

## Acceptance Criteria
- **AC-1 (happy path — title)**: Given a valid `id` and a non-empty `title` within limits, when updating, then the system persists the new title and refreshes `updatedAt`.
- **AC-2 (happy path — body)**: Given a valid `id` and a non-empty `body` within limits, when updating, then the system persists the new body and refreshes `updatedAt`.
- **AC-3 (happy path — date)**: Given a valid `id` and a valid `date`, when updating, then the system persists the new date and refreshes `updatedAt`.
- **AC-4 (partial update)**: Given a valid `id` and any subset of `title`, `body`, `date`, when updating, then only provided fields change; others remain intact.
- **AC-5 (missing id)**: Given no `id`, when updating, then validation fails with `ID_REQUIRED`.
- **AC-6 (invalid id)**: Given a non-UUID `id`, when updating, then validation fails with `ID_INVALID`.
- **AC-7 (not found)**: Given a valid UUID that doesn’t exist, when updating, then validation fails with `ENTRY_NOT_FOUND`.
- **AC-8 (no fields)**: Given only `id` without any fields to update, when updating, then validation fails with `NO_FIELDS_TO_UPDATE`.
- **AC-9 (empty title)**: Given `title` that is empty after trimming, when updating, then validation fails with `TITLE_REQUIRED`.
- **AC-10 (title too long)**: Given `title` longer than 200 characters, when updating, then validation fails with `TITLE_TOO_LONG`.
- **AC-11 (empty body)**: Given `body` that is empty after trimming, when updating, then validation fails with `BODY_REQUIRED`.
- **AC-12 (body too long)**: Given `body` longer than 50000 characters, when updating, then validation fails with `BODY_TOO_LONG`.
- **AC-13 (invalid date)**: Given a `date` that doesn’t match `YYYY-MM-DD` or is not a real date, when updating, then validation fails with `DATE_INVALID`.
- **AC-14 (no-op)**: Given values identical to current ones, when updating, then the system reports `NO_CHANGES_APPLIED` and does not modify `updatedAt`.
