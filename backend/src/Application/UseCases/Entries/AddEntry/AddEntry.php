<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries\AddEntry;

use Daylog\Application\UseCases\Entries\AddEntry\AddEntryInterface;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryResponse;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryResponseInterface;
use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;

use Daylog\Application\Normalization\Entries\AddEntry\AddEntryInputNormalizer;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Class AddEntry
 *
 * UC-1: Create a new Entry from user input, validate per business rules,
 * persist via repository, and return the new Entry UUID.
 */
final class AddEntry implements AddEntryInterface
{
    /**
     * @param EntryRepositoryInterface $repo Repository responsible for persistence.
     * @param AddEntryValidatorInterface $validator Validator for business rules.
     */
    public function __construct(
        private EntryRepositoryInterface    $repo,
        private AddEntryValidatorInterface  $validator
    ) {}

    /**
     * Execute the use case.
     *
     * @param AddEntryRequestInterface $request User input DTO.
     *
     * @return AddEntryResponseInterface Response DTO with id and timestamps.
     */
    public function execute(AddEntryRequestInterface $request): AddEntryResponseInterface
    {
        // Validate request per business rules
        $this->validator->validate($request);

        // Normalize (adds id, createdAt, updatedAt)
        $params = AddEntryInputNormalizer::normalize($request);
        $entry  = Entry::fromArray($params);

        // Persist
        $this->repo->save($entry);

        // Response DTO
        $response = AddEntryResponse::fromEntry($entry);

        return $response;
    }
}