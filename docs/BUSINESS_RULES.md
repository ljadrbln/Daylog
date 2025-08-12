# Global Business Rules

These rules apply across the system unless a use case specifies otherwise.

## BR-1 Title length
- The title must be between 1 and 200 characters after trimming.

## BR-2 Body length
- The body must be between 1 and 50000 characters.

## BR-3 Trimming
- Inputs are trimmed (leading/trailing whitespace removed) before validation.

## BR-4 Timestamps (consistency & monotonicity)
- All timestamps are stored in UTC (ISO-8601).
- On creation, a single time snapshot is taken: `createdAt = updatedAt = Clock.now()`.
- `createdAt` is immutable.
- On updates: `updatedAt := max(previous.updatedAt, Clock.now())`.
- Invariants: `updatedAt >= createdAt` must always hold.

## BR-5 Status
- New entries default to `published` (no draft workflow in v1).

## BR-6 Entry date
- On creation, the client must provide an explicit date in format `YYYY-MM-DD` (ISO-8601, date only).
- The date must be a valid calendar date and not empty.
- The date is independent from timestamps (`createdAt`, `updatedAt`) and represents the logical date of the entry.

