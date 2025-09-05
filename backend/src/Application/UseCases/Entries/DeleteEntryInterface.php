<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryResponseInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Use Case Contract: UC-4 DeleteEntry.
 *
 * Purpose:
 * Define the application-level boundary for deleting a diary entry by its UUIDv4 identifier.
 * The implementation must validate input, delegate deletion to the repository/storage,
 * and return a response DTO confirming success.
 *
 * Mechanics:
 * - Accepts a request DTO carrying the entry id.
 * - Applies transport/domain validation; may throw on invalid id or when entry does not exist.
 * - Delegates to repository to delete the entry.
 * - Returns a response DTO indicating the outcome of the operation.
 *
 * Error cases:
 * - Invalid id (format/content) → TransportValidationException (e.g., ID_INVALID).
 * - Entry not found → DomainValidationException with code ENTRY_NOT_FOUND.
 */
interface DeleteEntryInterface
{
    /**
     * Execute UC-4 DeleteEntry.
     *
     * @param DeleteEntryRequestInterface $request Input DTO containing the entry UUID.
     * @return DeleteEntryResponseInterface Response DTO indicating deletion outcome.
     *
     * @throws TransportValidationException When the request payload fails transport-level validation.
     * @throws DomainValidationException    When the entry is not found or violates domain constraints.
     */
    public function execute(DeleteEntryRequestInterface $request): DeleteEntryResponseInterface;
}
