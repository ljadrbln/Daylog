<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries\DeleteEntry;

use Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntryInterface;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryResponse;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryResponseInterface;
use Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidatorInterface;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;

use Daylog\Application\Exceptions\NotFoundException;
use Daylog\Application\Exceptions\DomainValidationException;

/**
 * UC-4 DeleteEntry — application service.
 *
 * Purpose:
 * Orchestrate validation and repository deletion for a single entry by UUID.
 *
 * Mechanics:
 * - Validate request first.
 * - Extract id from request and delegate deletion to repository.
 * - Return a response DTO echoing the deleted id.
 */
final class DeleteEntry implements DeleteEntryInterface
{
    /** @var DeleteEntryValidatorInterface */
    private DeleteEntryValidatorInterface $validator;

    /** @var EntryRepositoryInterface */
    private EntryRepositoryInterface $repo;

    /**
     * @param EntryRepositoryInterface      $repo
     * @param DeleteEntryValidatorInterface $validator
     */
    public function __construct(EntryRepositoryInterface $repo, DeleteEntryValidatorInterface $validator)
    {
        $this->repo      = $repo;
        $this->validator = $validator;
    }

    /**
     * Execute UC-4: Delete Entry.
     *
     * Purpose:
     * Orchestrates the use case of removing an existing Entry identified by UUID v4.
     * The request is validated, the Entry is looked up, and if found, it is deleted
     * from storage. A response DTO is returned with a snapshot of the deleted Entry.
     *
     * @param DeleteEntryRequestInterface $request DTO carrying the target entry id.
     * @return DeleteEntryResponseInterface DTO wrapping the deleted Entry snapshot.
     *
     * @throws DomainValidationException If the provided id violates UC constraints
     *                                   (e.g., not a strict UUID v4).
     * @throws NotFoundException         If no Entry with the given id exists in storage.
     */
    public function execute(DeleteEntryRequestInterface $request): DeleteEntryResponseInterface
    {
        // Validate request per business rules
        $this->validator->validate($request);

        $entryId = $request->getId();
        $entry   = $this->repo->findById($entryId);

        if (is_null($entry)) {
            $errorCode = 'ENTRY_NOT_FOUND';
            $exception = new NotFoundException($errorCode);

            throw $exception;
        }

        // Delete
        $this->repo->deleteById($entryId);

        // Response DTO
        $response = DeleteEntryResponse::fromEntry($entry);

        return $response;
    }
}
