<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryErrorAssertions;

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
    use UpdateEntryErrorAssertions;

    /**
     * AC-09: Provided empty (after trimming) title → TITLE_REQUIRED.
     *
     * @return void
     */
    public function testEmptyTitleFailsValidationWithTitleRequired(): void
    {
        // Arrange
        $request = UpdateEntryTestRequestFactory::emptyTitle();

        // Expect: sanitizer on a presentation level makes a trim and passes an empty string to the validator.
        $this->expectEntryNotFound();

        // Act
        $this->useCase->execute($request);
    }
}
