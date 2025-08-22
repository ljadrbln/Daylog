# Global Business Rules

These rules apply across the system unless a use case specifies otherwise.

## BR-1 Trimming
- Inputs are trimmed (leading/trailing whitespace removed) before validation.

## BR-2 Timestamps (consistency & monotonicity)
- All timestamps are stored in UTC (ISO-8601).
- On creation, a single time snapshot is taken: `createdAt = updatedAt = Clock.now()`.
- `createdAt` is immutable.
- On updates: `updatedAt := max(previous.updatedAt, Clock.now())`.
- Invariants: `updatedAt >= createdAt` must always hold.

Implementation note: strict calendar date validation is implemented by DateService::isValidLocalDate().


