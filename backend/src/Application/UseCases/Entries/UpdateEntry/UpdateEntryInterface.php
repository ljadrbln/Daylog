<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryResponseInterface;

/**
 * Use Case Contract: UC-5 UpdateEntry.
 *
 * Purpose:
 * Define the application-level boundary for updating an existing diary entry.
 * The implementation must validate input according to UC-5 (see ENTRY-BR-1..ENTRY-BR-2 and BR-1..BR-2),
 * apply partial changes to the target Entry, persist them, and return the updated domain model.
 *
 * Mechanics:
 * - Accepts a DTO with: required `id` (UUID v4) and at least one optional field among `title`, `body`, `date`.
 * - Applies domain validation for provided fields and may throw a DomainValidationException on AF-1..AF-9 cases
 *   (e.g., ID_REQUIRED, ID_INVALID, NO_FIELDS_TO_UPDATE, TITLE_REQUIRED/TOO_LONG, BODY_REQUIRED/TOO_LONG, DATE_INVALID).
 * - On success, updates only provided fields; other fields remain intact.
 * - Refreshes `updatedAt` according to BR-2 (monotonicity), keeps `createdAt` immutable.
 * - Returns the persisted Entry as the result of execution.
 *
 * Notes:
 * - Transport-level concerns (raw types/presence before DTO construction) are out of scope here.
 * - Implementations SHOULD handle the informational no-op scenario (AF-10 NO_CHANGES_APPLIED) per UC-5 policy.
 *
 * @see docs/use-cases/UC-5-UpdateEntry.md for parameters, limits, alternative flows, and acceptance criteria.
 */
interface UpdateEntryInterface
{
    /**
     * Execute UC-5 UpdateEntry.
     *
     * Scenario:
     * Receives a validated transport DTO, enforces UC-5 domain rules for optional fields,
     * persists effective changes, and returns a response DTO carrying the updated Entry.
     *
     * @param UpdateEntryRequestInterface $request Input DTO with id and optional fields (title/body/date).
     * @return UpdateEntryResponseInterface Response DTO with updated entry and timestamps.
     */
    public function execute(UpdateEntryRequestInterface $request): UpdateEntryResponseInterface;
}
