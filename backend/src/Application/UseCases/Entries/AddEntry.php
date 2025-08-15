<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries;

use Daylog\Application\Validators\Entries\AddEntryValidatorInterface;
use Daylog\Application\DTO\Entries\AddEntryRequestInterface;
use Daylog\Domain\Interfaces\EntryRepositoryInterface;
use Daylog\Domain\Models\Entry;

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
        private EntryRepositoryInterface $repo,
        private AddEntryValidatorInterface $validator
    ) {}

    /**
     * Execute the use case.
     *
     * @param AddEntryRequestInterface $request Request DTO with title, body, date.
     * @return string UUID of the newly created entry.
     */
    public function execute(AddEntryRequestInterface $request): string
    {
        // Validate request per business rules
        $this->validator->validate($request);

        $data = [
            'title' => trim($request->getTitle()),
            'body'  => trim($request->getBody()),
            'date'  => trim($request->getDate())
        ];

        $entry = Entry::fromArray($data);

        $uuid = $this->repo->save($entry);

        return $uuid;
    }
}