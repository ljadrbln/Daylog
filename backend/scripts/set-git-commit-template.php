<?php
declare(strict_types=1);

/**
 * Ensure .gitmessage is registered as commit template.
 * Cross-platform and safe for CI.
 */

$root = dirname(__DIR__, 2);
$file = $root . DIRECTORY_SEPARATOR . '.gitmessage';

if (!is_file($file)) {
    fwrite(STDERR, "Commit template not found: {$file}\n");
    exit(0); // not fatal
}

$cmd = sprintf('git config commit.template %s', escapeshellarg($file));
exec($cmd, $out, $code);

if ($code !== 0) {
    fwrite(STDERR, "Failed to set commit.template (is this a git repo? is git available?)\n");
    exit(0);
}

echo "Commit template set to {$file}\n";
