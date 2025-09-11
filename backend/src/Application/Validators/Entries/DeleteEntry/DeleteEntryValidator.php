<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries\DeleteEntry;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Rules\Common\IdRule;

/**
 * Validates business rules for DeleteEntry request.
 *
 * Purpose:
 * Ensure that the incoming identifier is a valid RFC-4122 UUID
 * Ignores transport-level concerns (presence/types) — they belong to the Presentation layer.
 *
 * Mechanics:
 * - Reads id from the request DTO.
 * - Delegates UUID shape validation to UuidGenerator::isValid().
 * - Throws DomainValidationException with a single error code on failure.
 *
 * Transport-level checks (types/presence) are NOT performed here.
 */
final class DeleteEntryValidator implements DeleteEntryValidatorInterface
{
    /**
     * Validate DeleteEntry request against domain rules.
     *
     * @param DeleteEntryRequestInterface $request DTO with the target identifier.
     * @return void
     *
     * @throws DomainValidationException When id is not a valid RFC-4122 UUID.
     */
    public function validate(DeleteEntryRequestInterface $request): void
    {
        $this->validateId($request);
    }

    /**
     * Validate that the id is a valid UUID.
     *
     * @param DeleteEntryRequestInterface $request
     * @return void
     */
    private function validateId(DeleteEntryRequestInterface $request): void
    {
        $entryId = $request->getId();
        IdRule::assertValidRequired($entryId);
    }
}
