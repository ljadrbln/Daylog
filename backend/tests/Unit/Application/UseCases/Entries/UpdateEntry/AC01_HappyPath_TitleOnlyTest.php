<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\DateService;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * UC-5 / AC-01 — Happy path (title-only) for UpdateEntry use case.
 *
 * Purpose:
 * Ensure that when only the title is provided along with a valid id, the use case
 * updates the title, preserves other fields, refreshes updatedAt per BR-2, and
 * returns a response DTO with a valid domain Entry snapshot.
 *
 * Mechanics:
 * - Seed the fake repository with a valid Entry from EntryTestData::getOne().
 * - Build UpdateEntryRequest with {id, title} only.
 * - Validator is mocked in the base (expected to run exactly once and succeed).
 * - Execute the use case and assert:
 *   • id is preserved and is a valid UUID v4,
 *   • title reflects the new value,
 *   • body and date remain unchanged,
 *   • timestamps are ISO-8601 UTC and updatedAt ≥ createdAt.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC01_HappyPath_TitleOnlyTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate title-only update behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathUpdatesTitleOnlyAndReturnsResponseDto(): void
    {
        // Arrange: seed an existing entry
        $seedData = EntryTestData::getOne();
        $existing = Entry::fromArray($seedData);

        $repo = $this->makeRepo();
        $repo->save($existing);

        $id       = $existing->getId();
        $newTitle = 'Updated title';

        $payload = [
            'id'    => $id,
            'title' => $newTitle,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $validator = $this->makeValidatorOk();

        // Act
        $useCase  = $this->makeUseCase($repo, $validator);
        $response = $useCase->execute($request);

        // Assert
        $entry = $response->getEntry();

        $entryId   = $entry->getId();
        $isValidId = UuidGenerator::isValid($entryId);
        $this->assertTrue($isValidId);
        $this->assertSame($id, $entryId);

        $this->assertSame($newTitle,          $entry->getTitle());
        $this->assertSame($seedData['body'],  $entry->getBody());
        $this->assertSame($seedData['date'],  $entry->getDate());

        $createdAt = $entry->getCreatedAt();
        $updatedAt = $entry->getUpdatedAt();

        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($createdAt);
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($updatedAt);

        $this->assertTrue($isCreatedAtValid);
        $this->assertTrue($isUpdatedAtValid);
        $this->assertGreaterThanOrEqual(strtotime($createdAt), strtotime($updatedAt));
    }
}
