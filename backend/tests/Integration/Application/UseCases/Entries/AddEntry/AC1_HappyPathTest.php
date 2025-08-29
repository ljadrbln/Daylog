<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * AC-1: Given valid title/body/date, the system returns a new id and persists the entry.
 *
 * Purpose:
 *   Verify the happy path using real wiring (Provider + SqlFactory) and a clean DB
 *   prepared in the base class.
 *
 * Mechanics:
 *   - Build a valid request from fixture data.
 *   - Execute the use case.
 *   - Assert: returned id is a valid UUIDv4 and DB contains exactly one row.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 */
final class AC1_HappyPathTest extends BaseAddEntryIntegrationTest
{
    /**
     * AC-1 Happy path: persists and returns UUID.
     *
     * @return void
     */
    public function testHappyPathPersistsAndReturnsUuid(): void
    {
        // Arrange
        $data = EntryTestData::getOne();

        /** @var AddEntryRequestInterface $request */
        $requestClass = AddEntryRequest::class;
        $request      = $requestClass::fromArray($data);

        // Act
        $response = $this->useCase->execute($request);
        $uuid     = $response->getId();

        // Assert
        $isValid = UuidGenerator::isValid($uuid);
        $this->assertTrue($isValid);

        $rowsCount = EntryFixture::countRows();
        $this->assertSame(1, $rowsCount);
    }
}
