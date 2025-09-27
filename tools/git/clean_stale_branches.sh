#!/usr/bin/env bash
set -euo pipefail

REMOTE="${REMOTE:-origin}"
BASES=("main" "master" "develop")
FORCE=0

if [[ "${1:-}" == "-f" ]]; then
  FORCE=1
fi

git fetch --all --prune
current_branch="$(git rev-parse --abbrev-ref HEAD)"

is_protected() {
  local b="$1"
  for p in "${BASES[@]}"; do
    [[ "$b" == "$p" ]] && return 0
  done
  [[ "$b" == "$current_branch" ]] && return 0
  return 1
}

delete_branch() {
  local b="$1"
  if [[ $FORCE -eq 1 ]]; then
    echo "Deleting branch: $b"
    git branch -D "$b"
  else
    echo "[dry-run] Would delete branch: $b"
  fi
}

# перебор веток-кандидатов
for branch in $(git for-each-ref --format='%(refname:short)' refs/heads/); do
  if ! is_protected "$branch"; then
    upstream=$(git rev-parse --abbrev-ref "$branch@{upstream}" 2>/dev/null || true)
    if [[ -z "$upstream" ]] || ! git ls-remote --exit-code "$REMOTE" "$upstream" >/dev/null 2>&1; then
      delete_branch "$branch"
    fi
  fi
done

if [[ $FORCE -eq 0 ]]; then
  echo
  echo "Nothing was deleted. Run again with -f to actually delete."
fi

