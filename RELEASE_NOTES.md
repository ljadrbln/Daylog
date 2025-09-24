# v1.0.0 — 2025-09-24

## Highlights
- First public release of **Daylog**, a journaling application built with Clean Architecture and TDD from day one.
- Core entry lifecycle implemented as separate use cases: **AddEntry**, **ListEntries**, **GetEntry**, **UpdateEntry**, **DeleteEntry**.
- End-to-end testing strategy with **Codeception** (Unit, Integration, Functional).
- Static analysis and code style checks configured: **PHPMD**, **PHPCS**, **PHPStan**.
- Infrastructure boundary with **Fat-Free Framework (F3)** in Presentation and DB layer; repositories and fixtures for predictable tests.

## Features
- Clean separation of layers: Domain / Application / Infrastructure / Presentation.
- Request DTOs, validators, and domain rules for entries with explicit constraints.
- REST-style endpoints for entry operations with consistent JSON responses.
- Deterministic data fixtures for Integration/Functional tests.
- Composer-based local development workflow.

## Fixes & Improvements
- Validation edge cases covered (length limits, trimming, invalid dates).
- Pagination bounds and query handling stabilized.
- Unified HTTP/JSON contracts across controllers.
- Test scaffolding and helpers consolidated for reuse.

## Breaking changes
- **None** — this is the first public release.

## System requirements
- PHP **8.1+**
- PHP extensions: **mbstring**
- Composer

## Install & Run (quick start)
```bash
git clone <your-repo-url>
cd daylog
composer install
# run the test suite
vendor/bin/codecept run
```

## Quality checks (optional)
```bash
# adjust paths/configs to your project layout
vendor/bin/phpmd backend/ text phpmd.xml
vendor/bin/phpcs
vendor/bin/phpstan analyse
```

## Full diff
- See GitHub **Compare** for the complete list of commits:
  - https://github.com/ljadrbln/Daylog/compare/16aea8a...v1.0.0
  - Get `<FIRST_SHA>` with:
    ```bash
    git rev-list --max-parents=0 HEAD | head -1
    ```
---

If you spot an issue or have a feature request, please open an issue or a merge request. Thank you for trying Daylog!