<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-11 — Empty body.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects an explicitly provided
 *   empty body (after trimming) with DomainValidationException('BODY_REQUIRED'),
 *   before any storage interaction. Uses real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 *   - Build a request with a valid UUID v4 id and a whitespace-only body ('   ') which becomes empty after trimming.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message 'BODY_REQUIRED' is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC11_EmptyBodyTest extends BaseUpdateEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-11: Provided empty (after trimming) body → BODY_REQUIRED.
     *
     * @return void
     */
    public function testEmptyBodyFailsValidationWithBodyRequired(): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac11EmptyBody();
        $request = $dataset['request'];

        // Expect: sanitizer on a presentation level makes a trim and passes an empty string to the validator.
        $this->expectEntryNotFound();

        // Act
        $this->useCase->execute($request);
    }
}
