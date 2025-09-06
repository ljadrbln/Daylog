<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries\GetEntry;

use Daylog\Application\UseCases\Entries\GetEntry\GetEntryInterface;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryResponse;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryResponseInterface;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\GetEntry\GetEntryValidatorInterface;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\Entry;

/**
 * Use Case: GetEntry.
 *
 * Purpose:
 * Retrieve a single Entry by its identifier. The use case validates the request,
 * queries the repository, and returns the domain model when found; otherwise it raises
 * a domain-level error.
 *
 * Mechanics:
 * - validator->validate($request)
 * - $id := $request->getId()
 * - $entry := $repo->findById($id)
 * - if $entry === null → throw DomainValidationException(['ENTRY_NOT_FOUND'])
 * - else return $entry
 */
final class GetEntry implements GetEntryInterface
{
    /** @var EntryRepositoryInterface */
    private EntryRepositoryInterface $repo;

    /** @var GetEntryValidatorInterface */
    private GetEntryValidatorInterface $validator;

    /**
     * @param EntryRepositoryInterface     $repo      Entries repository (domain abstraction).
     * @param GetEntryValidatorInterface   $validator Domain validator for GetEntry.
     */
    public function __construct(EntryRepositoryInterface $repo, GetEntryValidatorInterface $validator)
    {
        $this->repo      = $repo;
        $this->validator = $validator;
    }

    /**
     * Execute UC: return Entry by id or raise ENTRY_NOT_FOUND.
     *
     * @param GetEntryRequestInterface $request DTO with target identifier.
     * @return GetEntryResponseInterface DTO with retrieved domain Entry.
     *
     * @throws DomainValidationException On invalid id or when entry is absent.
     */
    public function execute(GetEntryRequestInterface $request): GetEntryResponseInterface
    {
        // Validate request per business rules
        $this->validator->validate($request);

        $entryId = $request->getId();
        $entry   = $this->repo->findById($entryId);

        if ($entry === null) {
            $error     = 'ENTRY_NOT_FOUND';
            $exception = new DomainValidationException($error);

            throw $exception;
        }

        // Response DTO
        $response = GetEntryResponse::fromEntry($entry);

        return $response;
    }
}
