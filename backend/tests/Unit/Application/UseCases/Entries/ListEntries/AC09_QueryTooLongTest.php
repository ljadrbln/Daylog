<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\DataProviders\ListEntriesQueryTooLongDataProvider;
use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-2 / AC-9 — Overlong query (>30 after trim) — Unit.
 *
 * Purpose:
 * Ensure that queries longer than 30 characters (after trimming) are rejected by validation and the repository is never accessed.
 *
 * Mechanics:
 * - Build a request with an overlong 'query';
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
     * Overlong query must trigger QUERY_TOO_LONG and avoid repository access.
     *
     * @dataProvider provideTooLongQueries
     *
     * @param string $rawQuery
     * @return void
     */
    public function testOverlongQueryTriggersValidationError(string $rawQuery): void
    {
        // Arrange
        $request   = ListEntriesTestRequestFactory::query($rawQuery);
        $validator = $this->makeValidatorThrows('QUERY_TOO_LONG');
        $repo      = $this->makeRepo();

        // Expect
        $this->expectQueryTooLong();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);
    }
}
