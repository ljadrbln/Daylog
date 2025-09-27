# GitFlow

Daylog uses GitFlow as a branching model.  
It defines clear rules for feature development, release preparation, and hotfixes.  
All merges are done via Pull Requests, without fast-forward, to keep history explicit.

## Branches

- **master** — production branch, always stable, tagged for releases.
- **develop** — integration branch, accumulates features before release.
- **feature/\*** — short-lived branches for new features or refactors, branched from `develop`.
- **release/\*** — release preparation branches, created from `develop`, merged into `master` and back into `develop`.
- **hotfix/\*** — urgent fixes, created from `master`, merged into both `master` and `develop`.

## Rules

- Never push directly to `master` or `develop`.  
- All merges must go through Pull Requests.  
- Use **no fast-forward** merges to preserve commit history.  
- Tag each release on `master` with version `vX.Y.Z`.  
- Keep feature branches focused: one scope per branch.

## Workflow

1. Create a `feature/*` branch from `develop`.  
2. Implement changes with Conventional Commits.  
3. Merge the feature into `develop` via PR.  
4. When ready to release, create a `release/*` branch from `develop`.  
5. Finalize version, changelog, and tests.  
6. Merge `release/*` into `master`, tag the version, then merge back into `develop`.  
7. For urgent fixes, create a `hotfix/*` from `master`, then merge it into both `master` and `develop`.

---

See also [CONVENTIONAL_COMMITS.md](CONVENTIONAL_COMMITS.md).

