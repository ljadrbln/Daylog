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
 * UC-5 / AC-2 — Happy path (body-only) for UpdateEntry use case.
 *
 * Purpose:
 * Ensure that when only the body is provided along with a valid id, the use case
 * updates the body, preserves other fields, refreshes updatedAt per BR-2, and
 * returns a response DTO with a valid domain Entry snapshot.
 *
 * Mechanics:
 * - Seeds FakeEntryRepository with a valid Entry from EntryTestData::getOne().
 * - Builds UpdateEntryRequest with {id, body} only.
 * - Mocks validator to run exactly once; domain rule specifics are tested elsewhere.
 * - Asserts: id preservation and validity, field isolation (body only), ISO timestamps,
 *   and BR-2 monotonicity (updatedAt ≥ createdAt).
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC2_HappyPath_BodyOnlyTest extends Unit
{
    /**
     * Validate body-only update behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathUpdatesBodyOnlyAndReturnsResponseDto(): void
    {
        // Arrange: seed an existing entry
        $seedData = EntryTestData::getOne();
        $existing = Entry::fromArray($seedData);

        $repo = new FakeEntryRepository();
        $repo->save($existing);

        $id      = $existing->getId();
        $newBody = 'Updated body';

        $payload = [
            'id'   => $id,
            'body' => $newBody,
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
        $this->assertSame($id, $entryId);

        $this->assertSame($seedData['title'], $entry->getTitle());
        $this->assertSame($newBody,           $entry->getBody());
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
