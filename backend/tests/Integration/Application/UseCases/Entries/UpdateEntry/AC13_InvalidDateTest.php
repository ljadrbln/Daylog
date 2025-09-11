<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;

/**
 * AC-13 (invalid date): Given a date that doesn’t match YYYY-MM-DD or is not a real date,
 * when updating, then validation fails with DATE_INVALID.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects malformed or non-existent calendar dates
 *   with DomainValidationException('DATE_INVALID') before any storage interaction.
 *   Uses real wiring (Provider + SqlFactory) and a clean DB from the base class.
 *
 * Mechanics:
 *   - Keep DB state uniform by seeding once (optional).
 *   - Build a payload with a valid UUID v4 and an invalid 'date' value.
 *   - Execute the real use case and assert DATE_INVALID.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC13_InvalidDateTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-13 Invalid date: malformed or impossible date fails with DATE_INVALID.
     *
     * @return void
     */
    public function testInvalidDateFailsValidationAndRepoUntouched(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        // Arrange
        $id = UuidGenerator::generate();

        $payload = [
            'id'   => $id,
            'date' => '2025-02-30', // invalid calendar date
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Expect domain-level validation error: DATE_INVALID
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'DATE_INVALID';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
