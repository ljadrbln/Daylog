<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;

/**
 * AC-11 (empty body): Given body that is empty after trimming,
 * when updating, then validation fails with BODY_REQUIRED.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects an explicitly provided
 *   empty body (after trimming) with DomainValidationException('BODY_REQUIRED'),
 *   before any storage interaction. Uses real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 *   - Optionally seed one entry to keep fixture flow uniform.
 *   - Build a payload with a valid UUID v4 id and an empty body ('').
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message BODY_REQUIRED is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC11_EmptyBodyTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-11 Empty body: provided empty body fails with BODY_REQUIRED.
     *
     * @return void
     */
    public function testEmptyBodyFailsValidationWithBodyRequired(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        // Valid UUID v4; body provided but empty after trimming
        $id        = UuidGenerator::generate();
        $emptyBody = '';

        /** @var array<string,string> $payload */
        $payload = [
            'id'   => $id,
            'body' => $emptyBody,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Expect domain-level validation error: BODY_REQUIRED
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'BODY_REQUIRED';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
