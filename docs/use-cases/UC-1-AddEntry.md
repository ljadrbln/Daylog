# UC-1 — Add Entry

## Intent
Create a new diary entry in a single-user environment.

## Preconditions
- Application is running.
- The system will receive `title`, `body`, and `date` (YYYY-MM-DD).

## Main Success Scenario
1. The system receives `title`, `body`, and `date`.
2. The system validates inputs according to global business rules (BR-1..BR-3, BR-6).
3. The system creates a new Entry with an immutable EntryId, the given `date`, `createdAt`, and `updatedAt`.
4. The system persists the Entry.
5. The system returns the new EntryId.

## Postconditions
- A new Entry exists in storage with valid timestamps.
- `updatedAt == createdAt` on creation.

## Business Rules (referencing globals)
- BR-1 Title length (1..200) after trimming.
- BR-2 Body length (1..50000).
- BR-3 Trimming.
- BR-4 Timestamps consistency & monotonicity.
- BR-6 Entry date format and validity.


## Acceptance Criteria
- **AC-1 (happy path)**: Given a non-empty title and body within limits, when adding an entry, then the system returns a new id and the entry is persisted with correct timestamps.
- **AC-2 (empty title)**: Given an empty (after trimming) title, when adding an entry, then validation fails with error code `TITLE_REQUIRED`.
- **AC-3 (title too long)**: Given a title longer than 200 characters, when adding an entry, then validation fails with error code `TITLE_TOO_LONG`.
- **AC-4 (empty body)**: Given an empty (after trimming) body, when adding an entry, then validation fails with error code `BODY_REQUIRED`.
- **AC-5 (body too long)**: Given a body longer than 50000 characters, when adding an entry, then validation fails with error code `BODY_TOO_LONG`.
- **AC-6 (missing date)**: Given no date, when adding an entry, then validation fails with error code `DATE_REQUIRED`.
- **AC-7 (invalid date format)**: Given a date not matching YYYY-MM-DD, when adding an entry, then validation fails with error code `DATE_INVALID_FORMAT`.
- **AC-8 (invalid calendar date)**: Given a date like 2025-02-30, when adding an entry, then validation fails with error code `DATE_INVALID`.

