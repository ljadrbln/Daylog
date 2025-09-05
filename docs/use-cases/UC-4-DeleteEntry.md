# UC-4 — Delete Entry

## Intent
Delete an existing diary entry identified by its UUID.

## Preconditions
- Application is running.
- The system may contain one or more entries.

## Parameters & Limits
- `id`: string, must be a valid UUID (version 4).  
  Invalid format → validation error `ID_INVALID`.

## Main Success Scenario
1. The system receives `id`.
2. The system validates that `id` is a well-formed UUID.
3. The system checks that an entry with the given `id` exists.
4. The system deletes the entry from storage.
5. The system returns confirmation of deletion.

## Alternative / Error Flows
- **AF-1**: Invalid UUID format → validation error `ID_INVALID`.
- **AF-2**: Entry not found → validation error `ENTRY_NOT_FOUND`.

## Postconditions
- The entry is no longer present in storage.
- Other entries remain unchanged.

## Business Rules (referencing globals)
- BR-1 Trimming: whitespace removed before UUID validation.
- BR-2 Timestamps: deletion does not break monotonicity invariants; no updates are applied.

## Acceptance Criteria
- **AC-1 (happy path)**: Given a valid UUID of an existing entry, when deleting, then the system removes it and confirms success.
- **AC-2 (invalid id)**: Given an id not matching UUID v4, when deleting, then validation fails with `ID_INVALID`.
- **AC-3 (not found)**: Given a valid UUID that does not match any entry, when deleting, then validation fails with `ENTRY_NOT_FOUND`.
