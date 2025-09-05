<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryResponse;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryResponseInterface;
use Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidatorInterface;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;

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
final class DeleteEntry
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
        $this->validator = $validator;
        $this->repo      = $repo;
    }

    /**
     * Execute UC-4.
     *
     * @param DeleteEntryRequestInterface $request
     * @return DeleteEntryResponseInterface
     */
    public function execute(DeleteEntryRequestInterface $request): DeleteEntryResponseInterface
    {
        $this->validator->validate($request);

        $id = $request->getId();

        $affected = $this->repo->deleteById($id);

        $response = new DeleteEntryResponse($id);
        return $response;
    }
}
