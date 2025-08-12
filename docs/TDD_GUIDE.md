# TDD Guide (Daylog)

We follow the Red → Green → Refactor cycle.

## Cycle

1. **Red** — write a failing test that describes desired behavior.
2. **Green** — write the minimum code needed to make the test pass.
3. **Refactor** — improve the design while keeping tests green.

## Constraints
- Each step is a separate commit.
- Commit messages must indicate the step:
  - Red: `test(unit): red <description>`
  - Green: `feat(...)` or `fix(...)`
  - Refactor: `refactor(...)`
- Tests describe observable behavior, not internal implementation.
- Red failure messages must be clear and intentional.

## Benefits
- Forces clear requirements before coding.
- Keeps design flexible and modular.
- Ensures every line of code is covered by tests.

