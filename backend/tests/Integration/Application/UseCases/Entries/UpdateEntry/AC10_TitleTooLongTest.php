<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-10 — Title too long.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects an explicitly provided title
 *   longer than ENTRY-BR-1 (200 chars) with DomainValidationException('TITLE_TOO_LONG'),
 *   before any storage interaction. Uses real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 *   - Optionally seed one entry to keep fixture flow uniform.
 *   - Generate a valid UUID v4 and build a request with a title of length 201.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message 'TITLE_TOO_LONG' is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC10_TitleTooLongTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-10: Provided title > 200 chars → TITLE_TOO_LONG.
     *
     * @return void
     */
    public function testTitleTooLongFailsValidationWithTitleTooLong(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        $id = UuidGenerator::generate();

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::tooLongTitle($id);

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'TITLE_TOO_LONG';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
