<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryResponseInterface;
use Daylog\Domain\Models\Entries\Entry;

/**
 * Use Case Contract: UC-1 AddEntry.
 *
 * Purpose:
 * Define the application-level boundary for adding a new diary entry.
 * The implementation must validate input according to UC-1 (see ENTRY-BR-1..ENTRY-BR-2 and BR-1..BR-2),
 * persist the entry, and return the created domain model.
 *
 * Mechanics:
 * - Accepts a DTO request with user-provided fields (title, body, date).
 * - Applies validation and may throw a DomainValidationException on AF-1..AF-6 cases.
 * - On success, creates and persists an Entry with generated id, createdAt and updatedAt.
 * - Returns the persisted Entry as the result of execution.
 *
 * @see docs/use-cases/UC-1-AddEntry.md for parameters, limits, and acceptance criteria.
 */
interface AddEntryInterface
{
    /**
     * Execute UC-1 AddEntry.
     *
     * @param AddEntryRequestInterface $request Input DTO carrying title, body, date.
     * @return Entry Created domain model with id/createdAt/updatedAt set.
     */
    public function execute(AddEntryRequestInterface $request): AddEntryResponseInterface;
}
