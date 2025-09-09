<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Rules\Entries\TitleDomainRule;
use Daylog\Application\Validators\Rules\Entries\BodyDomainRule;
use Daylog\Application\Validators\Rules\Entries\DateDomainRule;

/**
 * Validates business rules for UpdateEntry request (UC-5).
 *
 * Purpose:
 * Ensure that the update operation receives at least one updatable field
 * (title/body/date) and that each provided field conforms to domain rules.
 * Transport-level concerns (types/presence of 'id', string casting, etc.)
 * are NOT handled here.
 *
 * Rules:
 * - At least one of {title, body, date} MUST be provided.
 * - If title is provided: it MUST be non-empty and length <= TITLE_MAX.
 * - If body  is provided: it MUST be non-empty and length <= BODY_MAX.
 * - If date  is provided: it MUST be a valid local date in YYYY-MM-DD format.
 *
 * Error codes (thrown via DomainValidationException):
 * - NO_FIELDS_TO_UPDATE  — when none of title/body/date are provided.
 * - TITLE_REQUIRED       — provided title is an empty string.
 * - TITLE_TOO_LONG       — provided title exceeds TITLE_MAX.
 * - BODY_REQUIRED        — provided body is an empty string.
 * - BODY_TOO_LONG        — provided body exceeds BODY_MAX.
 * - DATE_INVALID         — provided date is not a valid YYYY-MM-DD calendar date.
 */
final class UpdateEntryValidator implements UpdateEntryValidatorInterface
{
    /**
     * Validate domain rules for UpdateEntry.
     *
     * @param UpdateEntryRequestInterface $request Incoming DTO carrying optional fields.
     * @return void
     *
     * @throws DomainValidationException When domain-level rules are violated.
     */
    public function validate(UpdateEntryRequestInterface $request): void
    {
        $this->assertAtLeastOneFieldProvided($request);
        
        TitleDomainRule::assertValidOptional($request);
        BodyDomainRule::assertValidOptional($request);
        DateDomainRule::assertValidOptional($request);
    }

    /**
     * Assert that at least one updatable field is present in the request.
     *
     * @param UpdateEntryRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException
     */
    private function assertAtLeastOneFieldProvided(UpdateEntryRequestInterface $request): void
    {
        $title = $request->getTitle(); // ?string|null
        $body  = $request->getBody();  // ?string|null
        $date  = $request->getDate();  // ?string|null

        $hasTitle = $title !== null;
        $hasBody  = $body  !== null;
        $hasDate  = $date  !== null;

        if ($hasTitle === false && $hasBody === false && $hasDate === false) {
            $errorCode = 'NO_FIELDS_TO_UPDATE';
            $exception = new DomainValidationException($errorCode);

            throw $exception;
        }
    }
}
