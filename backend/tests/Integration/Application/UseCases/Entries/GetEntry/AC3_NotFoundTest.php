<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\GetEntry;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Presentation\Requests\Entries\GetEntry\GetEntryRequestFactory;
use Daylog\Tests\Support\Fixture\EntryFixture;

/**
 * AC-3 Not found: ensures that a valid UUID v4 which does not exist
 * leads to a domain-level "ENTRY_NOT_FOUND" error.
 *
 * Mechanics:
 * - Seed exactly one entry via EntryFixture to ensure the repository works.
 * - Build a request DTO with another (valid v4) UUID that is not present in DB.
 * - Execute the GetEntry use case.
 * - Expect DomainValidationException; verify the error list contains only "ENTRY_NOT_FOUND".
 *
 * Notes:
 * - The repository is legitimately touched in this scenario (unlike AC-2),
 *   but it returns null for the missing id; the use case translates this to a domain error.
 *
 * @covers \Daylog\Application\UseCases\Entries\GetEntry
 *
 * @group UC-GetEntry
 */
final class AC3_NotFoundTest extends BaseGetEntryIntegrationTest
{
    /**
     * Verifies that a valid-but-absent UUID triggers "ENTRY_NOT_FOUND".
     *
     * @return void
     */
    public function testValidAbsentUuidTriggersEntryNotFound(): void
    {
        // Arrange
        EntryFixture::insertRows(1);
        
        $missingId = UuidGenerator::generate();
        $payload   = ['id' => $missingId];

        /** @var GetEntryRequestInterface $request */
        $request = GetEntryRequestFactory::fromArray($payload);

        // Expectation
        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage('ENTRY_NOT_FOUND');

        // Act
        $this->useCase->execute($request);
    }
}
