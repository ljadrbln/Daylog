<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-12 — Body too long.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects an explicitly provided body
 *   longer than ENTRY-BR-2 (50000 chars) with DomainValidationException('BODY_TOO_LONG'),
 *   before any storage interaction. Uses real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 *   - Optionally seed one entry to keep fixture flow uniform.
 *   - Generate a valid UUID v4 and build a request with body length = 50001.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message 'BODY_TOO_LONG' is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC12_BodyTooLongTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-12: Provided body > 50000 chars → BODY_TOO_LONG.
     *
     * @return void
     */
    public function testBodyTooLongFailsValidationWithBodyTooLong(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        $id = UuidGenerator::generate();

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::tooLongBody($id);

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'BODY_TOO_LONG';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
