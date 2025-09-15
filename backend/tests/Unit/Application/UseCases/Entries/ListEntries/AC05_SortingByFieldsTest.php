<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;

use Daylog\Tests\Support\Scenarios\Entries\ListEntriesScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\DataProviders\ListEntriesSortingDataProvider;

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
final class AC05_SortingByFieldsTest extends BaseListEntriesUnitTest
{
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

        $repo      = $this->makeRepo();
        $request   = ListEntriesTestRequestFactory::fromOverrides($overrides);
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        EntriesSeeding::intoFakeRepo($repo, $rows);

        // Act
        $response = $useCase->execute($request);
        $items    = $response->getItems();

        // Assert
        $actualIds   = array_column($items, 'id');
        $expectedIds = $this->getExpectedIds($rows, $sortField, $sortDir);
        $this->assertSame($expectedIds, $actualIds);
    }

    /**
     * Build expected id order from seeded rows using the requested sort.
     *
     * Purpose:
     * Derive the expected UUID sequence at runtime without symbolic markers.
     * Uses primary sort by the requested timestamp field and a stable secondary
     * order by createdAt DESC (per UC-2 AC-8) to avoid flakiness on equal keys.
     *
     * Mechanics:
     * - $seeded rows must contain 'id', 'createdAt', 'updatedAt' (strings: 'Y-m-d H:i:s').
     * - Primary key: $sortField ∈ {'createdAt','updatedAt'}.
     * - Direction: $sortDir ∈ {'ASC','DESC'}.
     * - Tie-breaker: createdAt DESC.
     *
     * @param array<int,array<string,mixed>> $rows      Rows with real UUIDs.
     * @param string                         $sortField One of: 'createdAt'|'updatedAt'.
     * @param 'ASC'|'DESC'                   $sortDir   Sorting direction.
     *
     * @return array<int,string>                        Ordered list of expected UUIDs.
     */
    private function getExpectedIds(array $rows, string $sortField, string $sortDir): array
    {
        $pairs = [];

        foreach ($rows as $row) {
            $id        = $row['id'];
            $primary   = $row[$sortField];   // 'createdAt' or 'updatedAt'
            $createdAt = $row['createdAt'];  // for stable tie-breaker

            $pairs[] = [
                'id'        => $id,
                'primary'   => $primary,
                'createdAt' => $createdAt,
            ];
        }

        usort(
            $pairs,
            static function (array $a, array $b) use ($sortDir): int {
                /** @var string */
                $leftPrimary = $a['primary'];
                $leftPrimary = strtotime($leftPrimary);

                /** @var string */
                $rightPrimary = $b['primary'];
                $rightPrimary = strtotime($rightPrimary);

                $cmp = $leftPrimary <=> $rightPrimary;

                if ($cmp === 0) {
                    // Stable secondary: createdAt DESC (always, independent of $sortDir)
                    /** @var string */
                    $leftCreated = $a['createdAt'];
                    $leftCreated = strtotime($leftCreated);

                    /** @var string */
                    $rightCreated = $b['createdAt'];
                    $rightCreated = strtotime($rightCreated);

                    $cmp = $rightCreated <=> $leftCreated;
                } elseif ($sortDir === 'DESC') {
                    $cmp = -$cmp;
                }

                return $cmp;
            }
        );


        $expectedIds = array_column($pairs, 'id');

        return $expectedIds;
    }    
}
