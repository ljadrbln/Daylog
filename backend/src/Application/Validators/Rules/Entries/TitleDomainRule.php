<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Rules\Entries;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
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
     * Validate required title (UC-1).
     *
     * @param AddEntryRequestInterface|UpdateEntryRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException
     */
    public static function assertValidRequired(
        AddEntryRequestInterface|UpdateEntryRequestInterface $request
    ): void {
        $title = $request->getTitle();

        if ($title === null || $title === '') {
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
     * Validate optional title (UC-5).
     *
     * @param AddEntryRequestInterface|UpdateEntryRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException
     */
    public static function assertValidOptional(
        AddEntryRequestInterface|UpdateEntryRequestInterface $request
    ): void {
        $title = $request->getTitle();

        if ($title === null) {
            return;
        }

        self::assertValidRequired($request);
    }
}
