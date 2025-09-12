<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-09 — Empty title.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects an explicitly provided empty title
 *   (after trimming) with DomainValidationException('TITLE_REQUIRED') before any storage interaction.
 *   Uses real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 *   - Build a request with a valid UUID v4 id and a whitespace-only title ('   ') which becomes empty after trimming.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message 'TITLE_REQUIRED' is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC09_EmptyTitleTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-09: Provided empty (after trimming) title → TITLE_REQUIRED.
     *
     * @return void
     */
    public function testEmptyTitleFailsValidationWithTitleRequired(): void
    {
        // Arrange
        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::emptyTitle();

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'TITLE_REQUIRED';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
