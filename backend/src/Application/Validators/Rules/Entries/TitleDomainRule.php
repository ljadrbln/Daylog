<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Rules\Entries;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Models\Entries\EntryConstraints;

/**
 * Domain rule for Entry title.
 *
 * Purpose:
 * Reusable domain-level checks for title in UC-1 (required) and UC-5 (optional).
 * Presentation guarantees types; here we validate content: non-empty + max length.
 *
 * Mechanics:
 * - assertValidRequired(): empty string → TITLE_REQUIRED; length overflow → TITLE_TOO_LONG.
 * - assertValidOptional(): if null → return; otherwise delegates to assertValidRequired().
 */
final class TitleDomainRule
{
    /**
     * Assert required title (UC-1): non-empty and within max length.
     *
     * @param string $title Non-empty title string after transport trimming.
     * @return void
     *
     * @throws DomainValidationException TITLE_REQUIRED|TITLE_TOO_LONG
     */
    public static function assertValidRequired(string $title): void
    {
        if ($title === '') {
            $message   = 'TITLE_REQUIRED';
            $exception = new DomainValidationException($message);

            throw $exception;
        }

        $tooLong = mb_strlen($title) > EntryConstraints::TITLE_MAX;
        if ($tooLong === true) {
            $message   = 'TITLE_TOO_LONG';
            $exception = new DomainValidationException($message);
            
            throw $exception;
        }
    }

    /**
     * Assert optional title (UC-5): if provided, must satisfy required rule.
     *
     * @param string|null $title Title or null when not provided.
     * @return void
     *
     * @throws DomainValidationException TITLE_REQUIRED|TITLE_TOO_LONG
     */
    public static function assertValidOptional(?string $title): void
    {
        if ($title === null) {
            return;
        }

        // Delegate to the required variant to avoid duplication.
        self::assertValidRequired($title);
    }
}
