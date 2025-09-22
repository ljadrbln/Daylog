<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * AC-08: Invalid calendar date → DATE_INVALID (API boundary).
 *
 * Purpose:
 *   Verify that the API rejects a payload whose date matches the YYYY-MM-DD pattern
 *   but is not a real calendar date (e.g., 2025-02-30), returning a standardized 422 envelope.
 *
 * Mechanics:
 *   - Build a canonical invalid JSON payload via AddEntryTestRequestFactory::invalidCalendarDatePayload();
 *   - POST payload to /api/entries using the shared base helper;
 *   - Assert 422 contract (success=false) and presence of DATE_INVALID in the flat errors list.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 *
 * @group UC-AddEntry
 */
final class AC08_InvalidCalendarDateCest extends BaseAddEntryFunctionalCest
{
    /**
     * API rejects non-existent calendar date and reports DATE_INVALID.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testInvalidCalendarDateIsRejectedWithDateInvalid(FunctionalTester $I): void
    {
        // Arrange
        $dataset = AddEntryDataset::ac08InvalidCalendarDate();

        // Act
        $this->addEntryFromDataset($I, $dataset);

        // Assert
        $this->assertUnprocessableContract($I);

        $code = 'DATE_INVALID';
        $this->assertErrorCode($I, $code);
    }
}