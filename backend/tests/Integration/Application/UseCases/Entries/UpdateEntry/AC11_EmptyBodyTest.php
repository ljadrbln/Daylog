<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-11 — Empty body.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects an explicitly provided
 *   empty body (after trimming) with DomainValidationException('BODY_REQUIRED'),
 *   before any storage interaction. Uses real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 *   - Optionally seed one entry to keep fixture flow uniform.
 *   - Build a request with a valid UUID v4 id and a whitespace-only body ('   ') which becomes empty after trimming.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message 'BODY_REQUIRED' is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC11_EmptyBodyTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-11: Provided empty (after trimming) body → BODY_REQUIRED.
     *
     * @return void
     */
    public function testEmptyBodyFailsValidationWithBodyRequired(): void
    {
        // Arrange
        $this->insertEntryWithPastTimestamps();

        $id = UuidGenerator::generate();

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::emptyBody($id);

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'BODY_REQUIRED';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
