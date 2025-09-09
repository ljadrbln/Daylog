<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries\AddEntry;

use Daylog\Domain\Services\DateService;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;

use Daylog\Application\Validators\Rules\Entries\TitleDomainRule;
use Daylog\Application\Validators\Rules\Entries\BodyDomainRule;
use Daylog\Application\Validators\Rules\Entries\DateDomainRule;

/**
 * Validates business rules for AddEntry request (UC-1).
 *
 * Purpose:
 * Enforce entity-level constraints for creating a new Entry:
 * title/body must be non-empty and within limits; date must be a valid local YYYY-MM-DD.
 * Transport-level concerns (presence/types) are explicitly out of scope here.
 *
 * Rules:
 * - Title: non-empty after trimming and length ≤ TITLE_MAX.
 * - Body:  non-empty after trimming and length ≤ BODY_MAX.
 * - Date:  strict local date in YYYY-MM-DD format and a real calendar date.
 *
 * Error codes (thrown via DomainValidationException):
 * - TITLE_REQUIRED  — title is empty after trimming.
 * - TITLE_TOO_LONG  — title exceeds TITLE_MAX.
 * - BODY_REQUIRED   — body is empty after trimming.
 * - BODY_TOO_LONG   — body exceeds BODY_MAX.
 * - DATE_REQUIRED   — date is missing or empty.
 * - DATE_INVALID    — date is not strict YYYY-MM-DD or not a real calendar date.
 */
final class AddEntryValidator implements AddEntryValidatorInterface
{
    /**
     * Validate domain rules for AddEntry.
     *
     * Scenario:
     * Receives a fully constructed AddEntry DTO (transport already done),
     * asserts required title/body/date against domain constraints, and throws on violations.
     *
     * @param AddEntryRequestInterface $request Incoming DTO with required fields.
     * @return void
     *
     * @throws DomainValidationException When one or more domain rules are violated.
     */
    public function validate(AddEntryRequestInterface $request): void
    {
        TitleDomainRule::assertValidRequired($request);
        BodyDomainRule::assertValidRequired($request);
        DateDomainRule::assertValidRequired($request);
    }
}

