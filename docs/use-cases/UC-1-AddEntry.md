# UC-1 — Add Entry

## Intent
Create a new diary entry in a single-user environment.

## Preconditions
- Application is running.

## Main Success Scenario
1. The system receives `title` and `body`.
2. The system validates inputs according to global business rules (BR-1..BR-3).
3. The system creates a new Entry with an immutable id, `createdAt`, and `updatedAt`.
4. The system persists the Entry.
5. The system returns the new Entry id.

## Postconditions
- A new Entry exists in storage with valid timestamps.
- `updatedAt == createdAt` on creation.

## Business Rules (referencing globals)
- BR-1 Title length (1..200) after trimming.
- BR-2 Body length (1..50000).
- BR-3 Trimming.
- BR-4 Timestamps consistency.

## Acceptance Criteria
- **AC-1 (happy path)**: Given a non-empty title and body within limits, when adding an entry, then the system returns a new id and the entry is persisted with correct timestamps.
- **AC-2 (empty title)**: Given an empty (after trimming) title, when adding an entry, then validation fails with error code `TITLE_REQUIRED`.
- **AC-3 (title too long)**: Given a title longer than 200 characters, when adding an entry, then validation fails with error code `TITLE_TOO_LONG`.
- **AC-4 (empty body)**: Given an empty (after trimming) body, when adding an entry, then validation fails with error code `BODY_REQUIRED`.
- **AC-5 (body too long)**: Given a body longer than 50000 characters, when adding an entry, then validation fails with error code `BODY_TOO_LONG`.
