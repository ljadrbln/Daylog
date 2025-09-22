<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * AC-07: Invalid date input format → DATE_INVALID (API boundary).
 *
 * Purpose:
 *   Verify that the API rejects dates not matching strict YYYY-MM-DD format and
 *   returns the standardized 422 validation envelope consistent with UC-1 rules.
 *
 * Mechanics:
 *   - Build a canonical invalid JSON payload via AddEntryTestRequestFactory::invalidDateFormatPayload();
 *   - POST the payload to /api/entries using the base helper;
 *   - Assert 422 contract (success=false) and presence of DATE_INVALID in the flat errors list.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 *
 * @group UC-AddEntry
 */
final class AC07_InvalidDateFormatCest extends BaseAddEntryFunctionalCest
{
    /**
     * API rejects non-YYYY-MM-DD date and reports DATE_INVALID.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testInvalidDateFormatIsRejectedWithDateInvalid(FunctionalTester $I): void
    {
        // Arrange
        $dataset = AddEntryDataset::ac07InvalidDateFormat();

        // Act
        $this->addEntryFromDataset($I, $dataset);

        // Assert
        $this->assertUnprocessableContract($I);

        $code = 'DATE_INVALID';
        $this->assertErrorCode($I, $code);
    }
}
