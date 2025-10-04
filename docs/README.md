# Daylog Documentation Index

Start here to navigate the project documentation.

## Overview

- [Project Charter](PROJECT_CHARTER.md)
- [Requirements (Overview)](REQUIREMENTS.md)
- [Architecture: ERD & Glossary](ARCHITECTURE_ERD_AND_GLOSSARY.md)
- [Global Business Rules](BUSINESS_RULES.md)
- [Install & Setup](INSTALL.md)

## Use Cases

- [Use Cases Index](USE_CASES.md)
- UC-1 details: [UC-1 — Add Entry](use-cases/UC-1-AddEntry.md)
- UC-2 details: [UC-2 — List Entries](use-cases/UC-2-ListEntries.md)
- UC-3 details: [UC-3 — Get Entry](use-cases/UC-3-GetEntry.md)
- UC-4 details: [UC-4 — Delete Entry](use-cases/UC-4-DeleteEntry.md)
- UC-5 details: [UC-5 — Update Entry](use-cases/UC-5-UpdateEntry.md)
- Template: [UC Template](use-cases/UC_TEMPLATE.md)

## Frontend

The frontend (TypeScript + Vite + Vitest) has its own tooling and workflow.  
See [`frontend/README.md`](./frontend/README.md) for details.

## Frontend Acceptance

- UC-2 details: [UC-2 — List Entries (Frontend AC)](frontend/acceptance/UC-2-ListEntries.md)


## Process & Conventions

- [Conventional Commits](CONVENTIONAL_COMMITS.md)
- [TDD Guide](TDD_GUIDE.md)

## Notes

- Documents are living artifacts. Each new use case is added under `docs/use-cases/` and linked from `USE_CASES.md`.
- Requirements remain format-agnostic for identifiers (e.g., `EntryId`), leaving storage details to Infrastructure.
- Backend use cases live under `docs/use-cases/` and describe business logic.
- Frontend acceptance criteria live under `docs/frontend/acceptance/` and describe how the client validates API responses.


