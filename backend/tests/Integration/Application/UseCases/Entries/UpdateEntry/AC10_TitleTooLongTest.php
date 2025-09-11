<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Models\Entries\EntryConstraints;

/**
 * AC-10 (title too long): Given title longer than 200 characters,
 * when updating, then validation fails with TITLE_TOO_LONG.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects an explicitly provided title
 *   that exceeds ENTRY-BR-1 (max 200 chars) with DomainValidationException('TITLE_TOO_LONG'),
 *   before any storage interaction. Uses real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 *   - Optionally seed one entry to keep fixture flow uniform.
 *   - Build a payload with a valid UUID v4 id and a title of length 201.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message TITLE_TOO_LONG is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC10_TitleTooLongTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-10 Title too long: provided title > 200 fails with TITLE_TOO_LONG.
     *
     * @return void
     */
    public function testTitleTooLongFailsValidationWithTitleTooLong(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        // Valid UUID v4; title length = 201 (> 200)
        $id         = UuidGenerator::generate();
        $titleLength = EntryConstraints::TITLE_MAX + 1;
        $longTitle   = str_repeat('A', $titleLength);

        /** @var array<string,string> $payload */
        $payload = [
            'id'    => $id,
            'title' => $longTitle,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Expect domain-level validation error: TITLE_TOO_LONG
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'TITLE_TOO_LONG';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
