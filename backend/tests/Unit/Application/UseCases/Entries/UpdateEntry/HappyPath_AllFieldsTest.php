<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Codeception\Test\Unit;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;

use Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry;
use Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidatorInterface;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Services\DateService;

use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * UC-5 / Happy path (all fields) for UpdateEntry use case.
 *
 * Purpose:
 * Ensure that when id and all updatable fields (title/body/date) are provided,
 * the use case validates input, persists the merged state, and returns a response
 * DTO containing the updated Entry snapshot.
 *
 * Mechanics:
 * - Seeds FakeEntryRepository with a valid Entry from EntryTestData::getOne().
 * - Builds UpdateEntryRequest with id + title + body + date (all provided).
 * - Mocks validator to run exactly once; does not test validation rules themselves.
 * - Asserts: id preservation and validity, updated field values, ISO timestamps,
 *   and BR-2 monotonicity (updatedAt ≥ createdAt).
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class HappyPath_AllFieldsTest extends Unit
{
    /**
     * Validates that UpdateEntry updates all provided fields and returns a proper response DTO.
     *
     * @return void
     */
    public function testHappyPathUpdatesAllFieldsAndReturnsResponseDto(): void
    {
        // Arrange
        $seedData = EntryTestData::getOne();
        $existing = Entry::fromArray($seedData);

        $repo = new FakeEntryRepository();
        $repo->save($existing);

        $newData = EntryTestData::getOne();

        $payload = [
            'id'    => $existing->getId(),
            'title' => $newData['title'],
            'body'  => $newData['body'],
            'date'  => $newData['date']
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $validatorClass = UpdateEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);
        $validator
            ->expects($this->once())
            ->method('validate');

        // Act
        $useCase  = new UpdateEntry($repo, $validator);
        $response = $useCase->execute($request);

        // Assert
        $entry = $response->getEntry();

        $entryId   = $entry->getId();
        $isValidId = UuidGenerator::isValid($entryId);
        $this->assertTrue($isValidId);
        $this->assertSame($payload['id'], $entryId);

        $this->assertSame($newData['title'], $entry->getTitle());
        $this->assertSame($newData['body'],  $entry->getBody());
        $this->assertSame($newData['date'],  $entry->getDate());

        $createdAt = $entry->getCreatedAt();
        $updatedAt = $entry->getUpdatedAt();

        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($createdAt);
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($updatedAt);

        $this->assertTrue($isCreatedAtValid);
        $this->assertTrue($isUpdatedAtValid);
        $this->assertGreaterThanOrEqual(strtotime($createdAt), strtotime($updatedAt));
    }
}
