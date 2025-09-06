<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries\DeleteEntry;

use Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntryInterface;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryResponse;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryResponseInterface;
use Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidatorInterface;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;

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
     * Execute UC-4 DeleteEntry.
     *
     * Purpose:
     * - Validate the request.
     * - Ensure the entry exists before deleting.
     * - Throw DomainValidationException if not found.
     * - On success, delete and return a response with the id.
     *
     * @param DeleteEntryRequestInterface $request
     * @return DeleteEntryResponseInterface
     *
     * @throws DomainValidationException ENTRY_NOT_FOUND when entity is absent.
     */
    public function execute(DeleteEntryRequestInterface $request): DeleteEntryResponseInterface
    {
        $this->validator->validate($request);

        $id = $request->getId();

        $entry = $this->repo->findById($id);
        if (is_null($entry)) {
            $errorCode = 'ENTRY_NOT_FOUND';
            $exception = new DomainValidationException($errorCode);
            
            throw $exception;
        }

        $this->repo->deleteById($id);

        $response = new DeleteEntryResponse($id);
        return $response;
    }
}
