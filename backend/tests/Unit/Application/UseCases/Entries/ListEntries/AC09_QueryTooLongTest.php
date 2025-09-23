<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\DataProviders\ListEntriesQueryTooLongDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-9 — Overlong query (>30 after trim) — Unit.
 *
 * Purpose:
 * Ensure that queries longer than 30 characters (after trimming) are rejected by
 * validation and execution stops before any repository access.
 *
 * Mechanics:
 * - Build a request via dataset with an overlong 'query';
 * - Configure validator mock to throw DomainValidationException('QUERY_TOO_LONG');
 * - Execute and assert the exception via shared validation assertions.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC09_QueryTooLongTest extends BaseListEntriesUnitTest
{
    use EntryValidationAssertions;
    use ListEntriesQueryTooLongDataProvider;

    /**
     * Overlong query must trigger QUERY_TOO_LONG.
     *
     * @dataProvider provideTooLongQueries
     *
     * @param string $rawQuery
     * @return void
     */
    public function testOverlongQueryTriggersValidationError(string $rawQuery): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac09QueryTooLong($rawQuery);

        $repo      = $this->makeRepo();
        $validator = $this->makeValidatorThrows('QUERY_TOO_LONG');
        $useCase   = $this->makeUseCase($repo, $validator);

        // Expect
        $this->expectQueryTooLong();

        // Act
        $request = $dataset['request'];
        $useCase->execute($request);
    }
}
