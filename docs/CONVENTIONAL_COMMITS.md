# Conventional Commits

Format: `<type>(optional-scope): <summary>`

**Types**
- `feat` — a new feature.
- `fix` — a bug fix.
- `refactor` — code change that neither fixes a bug nor adds a feature.
- `test` — adding or updating tests.
- `docs` — documentation only changes.
- `chore` — maintenance tasks.
- `build` — build system changes.
- `ci` — CI/CD configuration changes.
- `perf` — performance improvements.
- `style` — formatting only (no code changes).

**Rules** 
- English only.
- One TDD step = one commit.
- Do not bundle unrelated changes.

**Examples**
- `feat(app): add AddEntry use case`
- `test(unit): red test for AddEntry`
- `refactor(domain): extract Value Object EntryId`


---

See also [GITFLOW.md](GITFLOW.md).

