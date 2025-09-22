<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * AC-08: Invalid calendar date → DATE_INVALID.
 *
 * Purpose:
 *   Ensure that a date which matches the YYYY-MM-DD format but is not a real
 *   calendar date (e.g., 2025-02-30) triggers a business validation error.
 *
 * Mechanics:
 *   - Build a valid baseline payload.
 *   - Set date to "2025-02-30" (non-existent calendar date).
 *   - Expect DomainValidationException, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 * 
 * @group UC-AddEntry
 */
final class AC08_InvalidCalendarDateTest extends BaseAddEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-8 Negative path: invalid calendar date fails with DATE_INVALID.
     *
     * @return void
     */
    public function testInvalidCalendarDateFailsWithDateInvalid(): void
    {
        // Arrange
        $dataset = AddEntryDataset::ac08InvalidCalendarDate();

        // Expectation
        $this->expectDateInvalid();

        // Act
        $request = $dataset['request'];
        $this->useCase->execute($request);

        // Safety
        $message = 'DomainValidationException was expected for invalid calendar date';
        $this->fail($message);
    }
}
