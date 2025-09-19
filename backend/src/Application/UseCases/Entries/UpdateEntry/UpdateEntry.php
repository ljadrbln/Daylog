<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryResponse;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryResponseInterface;

use Daylog\Application\Exceptions\NotFoundException;
use Daylog\Application\Exceptions\DomainValidationException;

use Daylog\Application\Normalization\Entries\UpdateEntry\UpdateEntryInputNormalizer;
use Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidatorInterface;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\Entry;

/**
 * UC-5 UpdateEntry — application service.
 *
 * Purpose:
 * Orchestrate domain validation and partial update of an existing Entry by UUID.
 * Applies only provided fields (title/body/date), persists changes, and returns
 * the updated domain snapshot.
 *
 * Mechanics:
 * - Validate request via UpdateEntryValidatorInterface.
 * - Load current Entry by id; if absent, throw DomainValidationException('ENTRY_NOT_FOUND').
 * - Normalize update payload (preserve id/createdAt, refresh updatedAt per BR-2).
 * - Rebuild domain Entry from normalized params and persist via repository.
 * - Return UpdateEntryResponse created from the persisted Entry.
 *
 * Notes:
 * - Transport-level concerns (raw types, presence before DTO) are out of scope here.
 * - No-op semantics (values identical to current) may be handled by the normalizer
 *   according to UC-5 policy (e.g., NO_CHANGES_APPLIED).
 *
 * @see docs/use-cases/UC-5-UpdateEntry.md
 */
final class UpdateEntry implements UpdateEntryInterface
{
    /**
     * @param EntryRepositoryInterface        $repo      Repository responsible for persistence.
     * @param UpdateEntryValidatorInterface   $validator Validator for UC-5 domain rules.
     */
    public function __construct(
        private EntryRepositoryInterface $repo,
        private UpdateEntryValidatorInterface $validator
    ) {}

    /**
     * Execute UC-5: Update Entry.
     *
     * Purpose:
     * Handles partial update of an existing Entry identified by UUID v4.
     * Validates request parameters, loads the current Entry, applies provided
     * field changes (title, body, date), refreshes timestamps if modified,
     * persists the updated Entry, and returns a response DTO.
     *
     * @param UpdateEntryRequestInterface $request Input DTO with id and optional fields.
     * @return UpdateEntryResponseInterface Response DTO with updated entry snapshot.
     *
     * @throws DomainValidationException If input violates business rules
     *                                   (e.g., TITLE_TOO_LONG, BODY_REQUIRED, DATE_INVALID)
     *                                   or if no effective changes are detected (NO_CHANGES_APPLIED).
     * @throws NotFoundException         If no Entry with the given id exists in storage.
     */
    public function execute(UpdateEntryRequestInterface $request): UpdateEntryResponseInterface
    {
        // Validate request per business rules
        $this->validator->validate($request);

        // Load current entry
        $entryId = $request->getId();
        $current = $this->repo->findById($entryId);

        if (is_null($current)) {
            $errorCode = 'ENTRY_NOT_FOUND';
            $exception = new NotFoundException($errorCode);
            
            throw $exception;
        }        

        // Normalize (preserve id/createdAt, apply provided fields, refresh updatedAt)
        $params = UpdateEntryInputNormalizer::normalize($request, $current);
        $entry  = Entry::fromArray($params);

        if($current->equals($entry)) {
            $errorCode = 'NO_CHANGES_APPLIED';
            $exception = new DomainValidationException($errorCode);

            throw $exception;            
        }

        // Persist
        $this->repo->save($entry);

        // Response DTO
        $response = UpdateEntryResponse::fromEntry($entry);
        return $response;
    }
}
