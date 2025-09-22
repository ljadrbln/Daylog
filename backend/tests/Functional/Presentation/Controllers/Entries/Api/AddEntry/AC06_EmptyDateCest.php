<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * AC-06: Empty date → DATE_REQUIRED (API boundary).
 *
 * Purpose:
 *   Verify that the API rejects an empty (post-trim) date and responds with the
 *   standardized 422 validation envelope consistent with UC-1 and ENTRY-BR rules.
 *
 * Mechanics:
 *   - Build a canonical invalid JSON payload via AddEntryDataset::ac06EmptyDate();
 *   - POST payload to /api/entries using the shared base helper;
 *   - Assert 422 contract (success=false) and presence of DATE_REQUIRED in the flat errors list.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 *
 * @group UC-AddEntry
 */
final class AC06_EmptyDateCest extends BaseAddEntryFunctionalCest
{
    /**
     * API rejects empty date and reports DATE_REQUIRED.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testEmptyDateIsRejectedWithDateRequired(FunctionalTester $I): void
    {
        // Arrange
        $dataset = AddEntryDataset::ac06EmptyDate();

        // Act
        $this->addEntryFromDataset($I, $dataset);

        // Assert
        $this->assertUnprocessableContract($I);

        $code = 'DATE_REQUIRED';
        $this->assertErrorCode($I, $code);
    }
}
