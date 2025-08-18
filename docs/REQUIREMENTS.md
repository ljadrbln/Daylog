# Requirements (Overview)

This is a **single-user** journaling application. There are no roles or permissions in v1.

## Glossary
- **Entry** — a diary note with a title, body, timestamps (`createdAt`, `updatedAt`).

## Use Cases Overview
- **UC-1 Add Entry** — create a new entry with title and body, persisted with timestamps.
- **UC-2 List Entries** — display entries in a paginated list, with optional filters and sorting.

## Detailed Use Cases
- See `use-cases/UC-1-AddEntry.md`.
- See `use-cases/UC-2-ListEntries.md`.

## Global Business Rules
- See `BUSINESS_RULES.md` for cross-cutting rules (input length limits, trimming, etc.).