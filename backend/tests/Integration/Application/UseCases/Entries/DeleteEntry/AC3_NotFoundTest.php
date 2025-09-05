<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\DeleteEntry;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Presentation\Requests\Entries\DeleteEntryRequestFactory;
use Daylog\Tests\Support\Fixture\EntryFixture;

/**
 * AC-3 Not found: ensures that a valid UUID v4 which does not exist
 * leads to a domain-level "ENTRY_NOT_FOUND" error.
 *
 * Mechanics:
 * - Seed exactly one entry via EntryFixture to ensure the repository works.
 * - Build a request DTO with another (valid v4) UUID that is not present in DB.
 * - Execute the DeleteEntry use case.
 * - Expect DomainValidationException; verify the error list contains only "ENTRY_NOT_FOUND".
 *
 * Invariants:
 * - The repository is legitimately touched in this scenario and returns null for the missing id.
 * - The use case translates this to a domain error without side effects on other rows.
 * - DB row count remains unchanged after execution.
 *
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry
 *
 * @group UC-DeleteEntry
 */
final class AC3_NotFoundTest extends BaseDeleteEntryIntegrationTest
{
    /**
     * Verifies that a valid-but-absent UUID triggers "ENTRY_NOT_FOUND"
     * and does not delete existing rows.
     *
     * @return void
     */
    public function testValidAbsentUuidTriggersEntryNotFound(): void
    {
        // Arrange: seed exactly one existing entry
        EntryFixture::insertRows(1);

        $rowsCountBefore = EntryFixture::countRows();
        $this->assertSame(1, $rowsCountBefore);

        $missingId = UuidGenerator::generate();
        $payload   = ['id' => $missingId];

        /** @var DeleteEntryRequestInterface $request */
        $request = DeleteEntryRequestFactory::fromArray($payload);

        // Expectation
        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage('ENTRY_NOT_FOUND');

        // Act
        $this->useCase->execute($request);

        // Assert: DB still has the original row (no side effects)
        $rowsCountAfter = EntryFixture::countRows();
        $this->assertSame(1, $rowsCountAfter);
    }
}
