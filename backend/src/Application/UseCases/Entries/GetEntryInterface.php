<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Domain\Models\Entries\Entry;

/**
 * Use Case Contract: UC-3 GetEntry.
 *
 * Purpose:
 * Define the application-level boundary for retrieving a single diary entry by its UUIDv4 identifier.
 * The implementation must validate input, delegate lookup to the repository/storage, and return a hydrated domain model.
 *
 * Mechanics:
 * - Accepts a request DTO carrying the entry id.
 * - Applies transport/domain validation; may throw on invalid id or when entry does not exist.
 * - Delegates to repository to fetch the entry and returns the domain entity on success.
 *
 * Error cases:
 * - Invalid id (format/content) → TransportValidationException (e.g., ID_INVALID).
 * - Entry not found → DomainValidationException with code ENTRY_NOT_FOUND.
 */
interface GetEntryInterface
{
    /**
     * Execute UC-3 GetEntry.
     *
     * @param GetEntryRequestInterface $request Input DTO containing the entry UUID.
     * @return Entry Hydrated domain model corresponding to the requested id.
     *
     * @throws TransportValidationException When the request payload fails transport-level validation.
     * @throws DomainValidationException    When the entry is not found or violates domain constraints.
     */
    public function execute(GetEntryRequestInterface $request): Entry;
}
