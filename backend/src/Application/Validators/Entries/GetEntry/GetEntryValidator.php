<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries\GetEntry;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Rules\Common\IdRule;

/**
 * Validates business rules for GetEntry request.
 *
 * Purpose:
 * Ensure that the incoming identifier is a valid RFC-4122 UUID (v1..v5).
 * Ignores transport-level concerns (presence/types) — they belong to the Presentation layer.
 *
 * Mechanics:
 * - Reads id from the request DTO.
 * - Delegates UUID shape validation to UuidGenerator::isValid().
 * - Throws DomainValidationException with a single error code on failure.
 *
 * Transport-level checks (types/presence) are NOT performed here.
 */

final class GetEntryValidator implements GetEntryValidatorInterface
{
    /**
     * Validate GetEntry request against domain rules.
     *
     * @param GetEntryRequestInterface $request DTO with the target identifier.
     * @return void
     *
     * @throws DomainValidationException When id is not a valid RFC-4122 UUID.
     */
    public function validate(GetEntryRequestInterface $request): void
    {
        $this->validateId($request);
    }

    /**
     * @param GetEntryRequestInterface $request
     * @return void
     */
    private function validateId(GetEntryRequestInterface $request): void
    {
        $entryId = $request->getId();
        IdRule::assertValid($entryId);
    }    
}