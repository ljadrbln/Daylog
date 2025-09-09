# Requirements (Overview)

This is a **single-user** journaling application. There are no roles or permissions in v1.

## Glossary
- **Entry** — a diary note with a title, body, timestamps (`createdAt`, `updatedAt`).

## Use Cases Overview
- **UC-1 Add Entry** — create a new entry with title and body, persisted with timestamps.
- **UC-2 List Entries** — display entries in a paginated list, with optional filters and sorting.
- **UC-3 Get Entry** — return a single diary entry by its unique identifier, including all fields and timestamps.
- **UC-4 Delete Entry** — remove an existing diary entry identified by its UUID; return confirmation of deletion.
- **UC-5 Update Entry** — update an existing diary entry’s mutable fields (title, body, date) by id, refreshing updatedAt when changes are applied.

## Detailed Use Cases
- See `use-cases/UC-1-AddEntry.md`.
- See `use-cases/UC-2-ListEntries.md`.
- See `use-cases/UC-3-GetEntry.md`.
- See `use-cases/UC-4-DeleteEntry.md`.
- See `use-cases/UC-5-UpdateEntry.md`.

## Global Business Rules
- See `BUSINESS_RULES.md` for cross-cutting rules (input length limits, trimming, etc.).