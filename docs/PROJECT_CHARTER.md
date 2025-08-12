# Project Charter — Daylog

## Vision
Provide a personal journaling application built as a clean-architecture reference, demonstrating best practices in documentation, TDD, and layered design.

## Goals
- Serve as a textbook example of a clean architecture PHP project.
- Maintain high developer ergonomics (fast tests, clear boundaries).
- Preserve full history with Conventional Commits.

## Non-Goals
- Public social networking features (likes, follows) in v1.
- Rich WYSIWYG editor (basic markdown first).

## Stakeholders
- Project Owner / Developer
- Future contributors

## Success Metrics
- 90%+ unit test coverage for Domain and Application.
- Unit test run time < 3 seconds locally.
- CI pipeline completes in under 5 minutes.

## Risks
- Overengineering — mitigated by strict TDD and YAGNI.
- Framework coupling — mitigated by strict layering and review.

