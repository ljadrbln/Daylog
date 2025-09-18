#!/usr/bin/env bash
set -euo pipefail

SQL_DIR="backend/database/migrations"
SQL_FILES=("$SQL_DIR"/*.sql)

process_db() {
  local DB_URL="$1"
  local rest="${DB_URL#mysql://}"
  local creds="${rest%%@*}"
  local hostpart="${rest#*@}"
  local user="${creds%%:*}"
  local pass="${creds#*:}"
  local hostport="${hostpart%%/*}"
  local dbname="${hostpart#*/}"; dbname="${dbname%%\?*}"

  local host port
  if [[ "$hostport" == *:* ]]; then
    host="${hostport%%:*}"; port="${hostport#*:}"
  else
    host="$hostport"; port="3306"
  fi

  local mysql_cmd="mysql --protocol=TCP -h$host -P$port -u$user -p$pass"

  echo "🔍 Checking database '$dbname'..."

  if echo "USE \`$dbname\`;" | $mysql_cmd 2>/dev/null; then
    read -p "Database $dbname exists. Recreate? (y/N) " yn
    if [[ "$yn" == "y" || "$yn" == "Y" ]]; then
      echo "Dropping $dbname..."
      echo "DROP DATABASE \`$dbname\`;" | $mysql_cmd
      echo "Creating $dbname..."
      echo "CREATE DATABASE \`$dbname\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" | $mysql_cmd
      echo "📥 Loading migrations into $dbname..."
      for f in "${SQL_FILES[@]}"; do
        echo "  → $f"
        $mysql_cmd "$dbname" < "$f"
      done
      echo "✅ Done with $dbname."
    else
      echo "⏩ Skipped recreation of $dbname (no migrations applied)."
    fi
  else
    echo "Creating $dbname..."
    echo "CREATE DATABASE \`$dbname\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" | $mysql_cmd
    echo "📥 Loading migrations into $dbname..."
    for f in "${SQL_FILES[@]}"; do
      echo "  → $f"
      $mysql_cmd "$dbname" < "$f"
    done
    echo "✅ Done with $dbname."
  fi
}

# --- main ---
if [[ -z "${DAYLOG_DEV_DATABASE_URL:-}" ]]; then
  echo "❌ Missing DAYLOG_DEV_DATABASE_URL (main database). Abort." >&2
  exit 1
fi

process_db "$DAYLOG_DEV_DATABASE_URL"

if [[ -z "${DAYLOG_TEST_DATABASE_URL:-}" ]]; then
  echo "⚠️  Missing DAYLOG_TEST_DATABASE_URL (tests won't run)."
else
  process_db "$DAYLOG_TEST_DATABASE_URL"
fi
