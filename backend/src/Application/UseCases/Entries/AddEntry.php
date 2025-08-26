<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryResponse;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryResponseInterface;

use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\Entry;

/**
 * Class AddEntry
 *
 * UC-1: Create a new Entry from user input, validate per business rules,
 * persist via repository, and return the new Entry UUID.
 */
final class AddEntry
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

        $entry = [
            'title' => trim($request->getTitle()),
            'body'  => trim($request->getBody()),
            'date'  => trim($request->getDate())
        ];

        $entry  = Entry::fromArray($entry);
        $data  = $this->repo->save($entry);

        $response = AddEntryResponse::fromArray($data);

        return $response;
    }
}