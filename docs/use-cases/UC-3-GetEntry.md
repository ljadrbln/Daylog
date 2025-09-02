# UC-3 — Get Entry

## Intent
Return a single diary entry by its identifier.

## Preconditions
- Application is running.
- The system may or may not contain an entry with the given id.

## Parameters & Limits
- `id`: string, UUID v4 (strict format).  
  Validation is local to this UC (ID format). Other input limits for entries are covered by entry-specific BRs.

## Main Success Scenario
1. The system receives `id`.  
2. The system validates that `id` is a strict UUID v4.  
3. The system looks up the entry by id in storage.  
4. If found, the system returns the entry with fields: `id`, `title`, `body`, `date`, `createdAt`, `updatedAt`.  
   Returned timestamps must respect global invariants (see BR-2), and `date` is the logical entry date (see ENTRY-BR-4).

## Alternative / Error Flows
- **AF-1 (invalid id)**: If `id` is not a valid UUID v4 → error `ID_INVALID`.  
- **AF-2 (not found)**: If no entry exists with given `id` → error `ENTRY_NOT_FOUND`.

## Postconditions
- Either a full entry is returned, or a clear error (`ID_INVALID` / `ENTRY_NOT_FOUND`).

## Business Rules (referencing globals)
- **BR-2 Timestamps consistency & monotonicity** — returned `createdAt`/`updatedAt` must satisfy `updatedAt ≥ createdAt`; `createdAt` is immutable.  
- **ENTRY-BR-4 Entry date** — `date` is a strict `YYYY-MM-DD` logical date, distinct from timestamps.  

*(Note: BRs about lengths/trimming apply to entry creation/update; here they are invariants of stored data.)*

## Acceptance Criteria
- **AC-1 (happy path)**: Given a valid UUID v4 `id` that exists, when requesting the entry, then the system returns `id`, `title`, `body`, `date`, `createdAt`, `updatedAt`, and BR-2/ENTRY-BR-4 invariants hold.  
- **AC-2 (invalid id)**: Given a non-UUID `id`, when requesting, then validation fails with `ID_INVALID`.  
- **AC-3 (not found)**: Given a valid UUID `id` that does not exist, when requesting, then the system returns `ENTRY_NOT_FOUND`.
