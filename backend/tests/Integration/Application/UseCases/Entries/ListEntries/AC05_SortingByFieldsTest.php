<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\ListEntriesScenario;
use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\DataProviders\ListEntriesSortingDataProvider;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;
use Daylog\Tests\Support\Helper\ListEntriesExpectationHelper;

/**
 * AC-05: Sorting by createdAt/updatedAt supports ASC/DESC.
 *
 * Purpose:
 * Verify that 'sortField' and 'sortDir' change ordering by timestamps using real DB wiring.
 * Expected order is derived at runtime from seeded rows, removing coupling to hardcoded markers.
 *
 * Mechanics:
 * - Seed 3 deterministic rows with identical logical 'date' and distinct timestamps.
 * - Drive four cases via a data provider (createdAt ASC/DESC, updatedAt ASC/DESC).
 * - Compute expected id sequence by sorting the seeded rows in-memory by the requested key/direction.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * @group UC-ListEntries
 */
final class AC05_SortingByTimestampsTest extends BaseListEntriesIntegrationTest
{
    use ListEntriesExpectationHelper;
    use ListEntriesSortingDataProvider;

    /**
     * AC-05: runtime-derived expectations without symbolic markers.
     *
     * @dataProvider provideTimestampSortingCases
     *
     * @param string       $sortField One of: 'createdAt'|'updatedAt'
     * @param 'ASC'|'DESC' $sortDir   Sorting direction
     *
     * @return void
     */
    public function testSortingByTimestampsIsApplied(string $sortField, string $sortDir): void
    {
        // Arrange
        $dataset = ListEntriesScenario::ac05SortingByTimestamps();
        $rows    = $dataset['rows'];
        
        $overrides = [
            'sortField' => $sortField,
            'sortDir'   => $sortDir,
        ];

        $request = ListEntriesTestRequestFactory::fromOverrides($overrides);
        EntriesSeeding::intoDb($rows);

        // Act
        $response  = $this->useCase->execute($request);
        $items     = $response->getItems();
        $actualIds = array_column($items, 'id');

        // Assert
        $expectedIds = $this->buildExpectedIds($rows, $sortField, $sortDir);
        $this->assertSame($expectedIds, $actualIds);
    }
}
