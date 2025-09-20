<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-13 — Invalid date.
 *
 * Purpose:
 *   Ensure Application-layer validation rejects malformed or non-existent calendar dates
 *   with DomainValidationException('DATE_INVALID') before any storage interaction.
 *   Uses real wiring (Provider + SqlFactory) and a clean DB from the base class.
 *
 * Mechanics:
 *   - Generate a valid UUID v4 and build a request with an invalid 'date' value.
 *   - Execute the real use case and assert DATE_INVALID.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC13_InvalidDateTest extends BaseUpdateEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-13: Invalid date (malformed or impossible) → DATE_INVALID.
     *
     * @return void
     */
    public function testInvalidDateFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac13InvalidDate();
        $request = $dataset['request'];

        // Expect
        $this->expectDateInvalid();

        // Act
        $this->useCase->execute($request);
    }
}
