<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidatorInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Services\DateService;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * UC-5 / AC-4 — Partial update.
 *
 * Purpose:
 * Given a valid id and a subset of fields, only provided fields must change
 * while others remain intact. The updatedAt timestamp is refreshed per BR-2.
 *
 * Mechanics:
 * - Seed repository with a valid Entry from EntryTestData::getOne().
 * - Build request with {id, title, date} while omitting body entirely.
 * - Validator is expected to run exactly once (domain specifics tested elsewhere).
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC4_PartialUpdateTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that only provided fields are changed; others remain intact.
     *
     * @return void
     */
    public function testPartialUpdateChangesOnlyProvidedFields(): void
    {
        // Arrange
        $seedData = EntryTestData::getOne();
        $existing = Entry::fromArray($seedData);

        $repo = $this->makeRepo();
        $repo->save($existing);

        $id       = $existing->getId();
        $newTitle = 'Updated title';
        $newDate  = '2005-08-14';

        $payload = [
            'id'    => $id,
            'title' => $newTitle,
            'date'  => $newDate, // body intentionally omitted
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Validator: once, OK
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
        $this->assertSame($newDate,           $entry->getDate());

        $createdAt = $entry->getCreatedAt();
        $updatedAt = $entry->getUpdatedAt();

        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($createdAt);
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($updatedAt);

        $this->assertTrue($isCreatedAtValid);
        $this->assertTrue($isUpdatedAtValid);

        $createdTs = strtotime($createdAt);
        $updatedTs = strtotime($updatedAt);
        $this->assertGreaterThanOrEqual($createdTs, $updatedTs);
    }
}
