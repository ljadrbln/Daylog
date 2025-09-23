<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-07 — Single-date exact match — Unit.
 *
 * Purpose:
 * Verify that the single-date filter (`date=YYYY-MM-DD`) returns exclusively entries
 * whose logical `date` equals the target date; entries with other dates must be excluded.
 *
 * Mechanics:
 * - Build a deterministic dataset via ListEntriesDataset::ac07SingleDateExact();
 * - Seed the fake repository with provided rows;
 * - Use the request DTO prepared by the dataset (payload already contains `date`);
 * - Execute the use case and assert that only the exact-date matches are returned
 *   in the expected order (date DESC within the day).
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC07_SingleDateExactMatchTest extends BaseListEntriesUnitTest
{
    /**
     * AC-07: date=YYYY-MM-DD returns only exact logical-date matches.
     *
     * @return void
     */
    public function testSingleDateFilterReturnsOnlyExactMatches(): void
    {
        // Arrange
        $repo    = $this->makeRepo();
        $dataset = ListEntriesDataset::ac07SingleDateExact();
        $this->seedFromDataset($repo, $dataset);
        
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $request  = $dataset['request'];
        $response = $useCase->execute($request);
        $items    = $response->getItems();

        // Assert
        $this->assertCount(2, $items);

        $expectedIds = $dataset['expectedIds'];
        $actualIds   = array_column($items, 'id');

        $this->assertSame($expectedIds, $actualIds);
    }
}
