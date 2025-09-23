<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * AC-09: query longer than 30 chars (after trimming) fails with QUERY_TOO_LONG.
 *
 * Purpose:
 *   Ensure the 30-char limit is enforced after trimming. Overlong inputs
 *   must trigger DomainValidationException with code QUERY_TOO_LONG.
 *
 * Mechanics:
 *   - Build baseline request via factory;
 *   - inject overlong query (>30 chars after trim);
 *   - expect exception QUERY_TOO_LONG.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * @group UC-ListEntries
 */
final class AC09_QueryTooLongTest extends BaseListEntriesIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-09: Overlong query (>30 chars after trim) must raise QUERY_TOO_LONG.
     *
     * @dataProvider \Daylog\Tests\Support\DataProviders\ListEntriesQueryTooLongDataProvider::provideTooLongQueries
     *
     * @param string $rawQuery
     * @return void
     */
    public function testOverlongQueryFailsWithQueryTooLong(string $rawQuery): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac09QueryTooLong($rawQuery);

        // Expect
        $this->expectQueryTooLong();

        // Act
        $request = $dataset['request'];
        $this->useCase->execute($request);

        // Safety
        $message = 'Expected DomainValidationException for an overlong query';
        $this->fail($message);
    }
}
