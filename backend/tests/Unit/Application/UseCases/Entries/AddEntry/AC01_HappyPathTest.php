<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Domain\Services\DateService;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * UC-1 / AC-01 — Happy path — Unit.
 *
 * Purpose:
 * Verify that AddEntry validates, persists via repository, and returns a response DTO
 * with a valid UUID; all fields are propagated and timestamps are sane.
 *
 * Mechanics:
 * - Data source: EntryTestData::getOne() (title, body, date).
 * - Validator is expected to run once and succeed.
 * - Repository is fake in-memory store; response carries the created Entry.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC01_HappyPathTest extends BaseAddEntryUnitTest
{
    /**
     * Validate happy path behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathSavesEntryAndReturnsResponseDto(): void
    {
        // Arrange        
        $dataset = AddEntryDataset::ac01HappyPath();
                
        $validator = $this->makeValidatorOk();
        $repo      = $this->makeRepo();

        // Act
        $request  = $dataset['request'];
        $useCase  = $this->makeUseCase($repo, $validator);
        $response = $useCase->execute($request);

        // Assert
        $entry = $response->getEntry();

        $entryId   = $entry->getId();
        $isValidId = UuidGenerator::isValid($entryId);
        $this->assertTrue($isValidId);

        $payload = $dataset['payload'];
        $this->assertSame($payload['title'], $entry->getTitle());
        $this->assertSame($payload['body'],  $entry->getBody());
        $this->assertSame($payload['date'],  $entry->getDate());

        $createdAt = $entry->getCreatedAt();
        $updatedAt = $entry->getUpdatedAt();
        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($createdAt);
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($updatedAt);

        $this->assertTrue($isCreatedAtValid);
        $this->assertTrue($isUpdatedAtValid);
        $this->assertSame($createdAt, $updatedAt);
    }
}
