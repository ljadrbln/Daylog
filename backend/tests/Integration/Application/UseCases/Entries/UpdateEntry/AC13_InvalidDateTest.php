<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-13 — Invalid date.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects malformed or non-existent calendar dates
 *   with DomainValidationException('DATE_INVALID') before any storage interaction.
 *   Uses real wiring (Provider + SqlFactory) and a clean DB from the base class.
 *
 * Mechanics:
 *   - Keep DB state uniform by seeding once (optional).
 *   - Generate a valid UUID v4 and build a request with an invalid 'date' value.
 *   - Execute the real use case and assert DATE_INVALID.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC13_InvalidDateTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-13: Invalid date (malformed or impossible) → DATE_INVALID.
     *
     * @return void
     */
    public function testInvalidDateFailsValidationAndRepoUntouched(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        $id = UuidGenerator::generate();

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::invalidDate($id);

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'DATE_INVALID';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
