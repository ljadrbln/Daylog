<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;

/**
 * AC-9 (empty title): Given title that is empty after trimming,
 * when updating, then validation fails with TITLE_REQUIRED.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects an explicitly provided
 *   empty title (after trimming) with DomainValidationException('TITLE_REQUIRED'),
 *   before any storage interaction. Uses real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 *   - Optionally seed one entry to keep fixture flow uniform.
 *   - Build a payload with a valid UUID v4 id and an empty title ('').
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message TITLE_REQUIRED is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC09_EmptyTitleTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-9 Empty title: provided empty title fails with TITLE_REQUIRED.
     *
     * @return void
     */
    public function testEmptyTitleFailsValidationWithTitleRequired(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        // Valid UUID v4; title provided but empty after trimming
        $id         = UuidGenerator::generate();
        $emptyTitle = '';

        /** @var array<string,string> $payload */
        $payload = [
            'id'    => $id,
            'title' => $emptyTitle,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Expect domain-level validation error: TITLE_REQUIRED
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'TITLE_REQUIRED';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
