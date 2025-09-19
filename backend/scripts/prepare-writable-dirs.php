<?php
declare(strict_types=1);

require __DIR__ . '/lib/fs.php';

/**
 * Prepare writable directories (cross-platform, idempotent).
 *
 * Behavior:
 * - Always ensure backend/tmp and backend/logs exist.
 * - On Unix: chmod 0775, chown to $USER, chgrp to www-data (best-effort).
 * - On Windows: only mkdir; permission ops are skipped automatically.
 */

$backendRoot = dirname(__DIR__); // .../backend
$dirs = [
    $backendRoot . '/tmp',
    $backendRoot . '/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        $ok = mkdir($dir, 0775, true);
        if (!$ok && !is_dir($dir)) {
            fwrite(STDERR, "Failed to create directory: {$dir}\n");
            exit(1);
        }
        echo "Created: {$dir}\n";
    }
}

// Try chmod everywhere (may succeed in containers/WSL/macOS)
foreach ($dirs as $dir) {
    tryChmod($dir, 0775);
}

// POSIX-only ownership adjustments
if (hasPosix()) {
    $user  = getenv('USER') ?: get_current_user() ?: null;
    $group = 'www-data';

    foreach ($dirs as $dir) {
        if ($user) {
            tryChown($dir, $user);
        }
        tryChgrp($dir, $group);
    }
}

$green = getenv('NO_COLOR') ? '' : "\033[32m";
$reset = getenv('NO_COLOR') ? '' : "\033[0m";
$tick  = "✔";

echo "{$green}{$tick}{$reset} Writable directories are ready.\n";

