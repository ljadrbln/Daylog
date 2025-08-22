# Entry Business Rules (ENTRY-BR)

These rules define **entity-level** constraints and invariants for the `Entry` model.
They complement global, cross-cutting rules (see `BUSINESS_RULES.md`, e.g., trimming and timestamp invariants).

> Identifier scheme: `ENTRY-BR-<n>`

---

## ENTRY-BR-1 — Title length
- The title must be between **1 and 200** characters **after trimming**.
- Trimming is defined by the global rule (see `BR-1` in `BUSINESS_RULES.md`).

**Rationale**
- Keeps titles concise and searchable while allowing typical journal usage.

**Applies to**
- Create (`AddEntry`), Update (`UpdateEntry`).

---

## ENTRY-BR-2 — Body length
- The body must be between **1 and 50000** characters **after trimming**.
- Trimming is defined by the global rule (see `BR-1` in `BUSINESS_RULES.md`)..

**Rationale**
- Prevents empty bodies and unbounded payloads while supporting long-form notes.

**Applies to**
- Create (`AddEntry`), Update (`UpdateEntry`).

---

## ENTRY-BR-3 — Default status
- New entries **default to `published`**.
- No draft workflow in v1.

**Rationale**
- Daylog v1 is a single-user journal; drafts add complexity without user value.

**Applies to**
- Create (`AddEntry`).

---

## ENTRY-BR-4 — Logical entry date
- Client MUST provide an explicit **`YYYY-MM-DD`** date.
- The date MUST be a **valid calendar date** and **not empty**.
- The date is **independent** from timestamps (`createdAt`, `updatedAt`).
- Timestamp invariants and UTC storage are defined globally by `BR-2`.

**Rationale**
- The logical date is a first-class sorting/filtering dimension in Daylog.
- Separation from timestamps guarantees stable, user-intended chronology.

**Applies to**
- Create (`AddEntry`); used by filters in `ListEntries`.

---

## Cross-references (Global)
- **BR-1 Trimming:** Whitespace trimming happens before length checks.
- **BR-2 Timestamps (UTC, monotonicity):** `createdAt = updatedAt = Clock.now()` on creation; `updatedAt := max(prev.updatedAt, Clock.now())` on updates; invariant `updatedAt ≥ createdAt`.

---

## Notes for tests & docs
- UC documents should **reference `ENTRY-BR-*`** for Entry-specific constraints and **`BR-*`** for global ones.
- Validation tests SHOULD assert trimming-first semantics for title/body (global `BR-1`) and strict calendar dates for `ENTRY-BR-4`.