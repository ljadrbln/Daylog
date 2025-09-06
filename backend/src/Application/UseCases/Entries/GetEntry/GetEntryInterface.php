<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries\GetEntry;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryResponseInterface;
use Daylog\Application\Exceptions\DomainValidationException;

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
 * - Entry not found → DomainValidationException with code ENTRY_NOT_FOUND.
 */
interface GetEntryInterface
{
    /**
     * Execute UC-3 GetEntry.
     *
     * @param GetEntryRequestInterface $request Input DTO containing the entry UUID.
     * @return GetEntryResponseInterface DTO with retrieved domain Entry.
     *
     * @throws DomainValidationException    When the entry is not found or violates domain constraints.
     */
    public function execute(GetEntryRequestInterface $request): GetEntryResponseInterface;
}
