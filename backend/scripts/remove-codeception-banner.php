<?php
declare(strict_types=1);

/**
 * Remove the "stand-with-ukraine" banner concat from Codeception Run.php.
 * Works cross-platform. Safe if vendor not installed or pattern not found.
 */

$target = __DIR__ . '/../../vendor/codeception/codeception/src/Codeception/Command/Run.php';
if (!is_file($target)) {
    // Vendor not installed yet — exit quietly.
    echo "Vendor not found, skipping banner removal.\n";
    exit(0);
}

$code = file_get_contents($target);
if ($code === false) {
    fwrite(STDERR, "Cannot read: {$target}\n");
    exit(1);
}

/**
 * Original sed removed:  . ' https://stand-with-ukraine.pp.ua'
 * Make a robust regex: optional spaces, keep only exact concat fragment.
 */
$pattern = "~\\.\s*'\\s*https://stand-with-ukraine\\.pp\\.ua'~";
$updated = preg_replace($pattern, '', $code, -1, $count);
if ($updated === null) {
    fwrite(STDERR, "Regex error while patching {$target}\n");
    exit(1);
}

if ($count > 0) {
    $ok = file_put_contents($target, $updated);
    if ($ok === false) {
        fwrite(STDERR, "Cannot write: {$target}\n");
        exit(1);
    }
    echo "Removed banner concat from Run.php ({$count} occurrence(s)).\n";
} else {
    echo "Pattern not found in Run.php. Nothing to remove.\n";
}
