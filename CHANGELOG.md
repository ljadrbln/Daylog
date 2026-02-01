# Changelog

All notable changes will be documented in this file.

## [Unreleased]
- Nothing yet.

## [v1.1.0] — 2026-02-01

### Added
- Entries CRUD UI in Presentation layer using vanilla Web Components with stable `dl-*` tags.
- Entry pages: list (UC-2), view (UC-3), add (UC-1), edit (UC-5).
- Delete action from the entry view (UC-4).

### Changed
- Reusable entry form view shared by add/edit flows.
- ListEntries runner-throw now renders a generic error message.

---

## [v1.0.0] — 2025-09-24
- First public release of Daylog.
- Clean Architecture, TDD, Codeception (Unit/Integration/Functional).
- Static analysis: PHPMD, PHPCS, PHPStan.
- Use-cases: AddEntry, ListEntries, GetEntry, UpdateEntry, DeleteEntry.

[Unreleased]: https://github.com/ljadrbln/Daylog/compare/v1.1.0...HEAD
[v1.1.0]: https://github.com/ljadrbln/Daylog/compare/v1.1.0...v1.0.0
[v1.0.0]: https://github.com/ljadrbln/Daylog/releases/tag/v1.0.0
