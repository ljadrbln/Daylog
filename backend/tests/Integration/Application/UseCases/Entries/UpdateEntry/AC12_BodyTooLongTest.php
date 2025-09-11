<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Models\Entries\EntryConstraints;

/**
 * AC-12 (body too long): Given body longer than 50000 characters,
 * when updating, then validation fails with BODY_TOO_LONG.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects an explicitly provided body
 *   that exceeds ENTRY-BR-2 (max 50000 chars) with DomainValidationException('BODY_TOO_LONG'),
 *   before any storage interaction. Uses real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 *   - Optionally seed one entry to keep fixture flow uniform.
 *   - Build a payload with a valid UUID v4 id and a body of length 50001.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message BODY_TOO_LONG is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC12_BodyTooLongTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-12 Body too long: provided body > 50000 fails with BODY_TOO_LONG.
     *
     * @return void
     */
    public function testBodyTooLongFailsValidationWithBodyTooLong(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        // Valid UUID v4; body length = 50001 (> 50000)
        $id         = UuidGenerator::generate();
        $bodyLength = EntryConstraints::BODY_MAX + 1;
        $longBody  = str_repeat('B', $bodyLength);

        /** @var array<string,string> $payload */
        $payload = [
            'id'   => $id,
            'body' => $longBody,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Expect domain-level validation error: BODY_TOO_LONG
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'BODY_TOO_LONG';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
