<?php
declare(strict_types=1);

/**
 * FS helpers for cross-platform scripts.
 *
 * - Capability-based checks (POSIX functions may be absent on Windows).
 * - Best-effort chmod/chown/chgrp: return bool, do not throw.
 */

function isWindows(): bool
{
    if (defined('PHP_OS_FAMILY')) {
        return PHP_OS_FAMILY === 'Windows';
    }
    $result = stripos(PHP_OS, 'WIN') === 0;
    return $result;
}

function hasPosix(): bool
{
    $result = function_exists('posix_geteuid') && function_exists('posix_getpwuid');
    return $result;
}

function tryChmod(string $path, int $mode): bool
{
    $result = @chmod($path, $mode);
    return $result;
}

function tryChown(string $path, string $user): bool
{
    if (!function_exists('chown')) {
        return false;
    }
    $result = @chown($path, $user);
    return $result;
}

function tryChgrp(string $path, string $group): bool
{
    if (!function_exists('chgrp')) {
        return false;
    }
    $result = @chgrp($path, $group);
    return $result;
}
