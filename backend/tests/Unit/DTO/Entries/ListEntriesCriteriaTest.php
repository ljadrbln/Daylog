<?php

namespace Daylog\Tests\Unit\Application\DTO\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\ListEntriesCriteria;
use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;

/**
 * Tests for ListEntriesCriteria mapping and normalization.
 *
 * This suite verifies that the criteria:
 *  - maps request fields 1:1 without business validation;
 *  - applies defaults for page/perPage;
 *  - trims and normalizes query (empty -> null);
 *  - converts empty date strings to null, leaving valid YYYY-MM-DD as-is.
 *
 * @covers \Daylog\Application\Queries\Entries\ListEntriesCriteria
 */
final class ListEntriesCriteriaTest extends Unit
{
    /**
     * AC-1: Given a valid request, returns a criteria with mapped values.
     *
     * Data source: a stub of ListEntriesRequestInterface.
     * We check the getters for exact values without additional validation.
     */
    public function testFromRequestReturnsMappedValues(): void
    {
        $page     = 2;
        $perPage  = 20;
        $fromDate = '2025-08-01';
        $toDate   = '2025-08-21';
        $query    = 'notes';

        $reqClass = ListEntriesRequestInterface::class;
        $req      = $this->createMock($reqClass);
        $req
            ->method('getPage')
            ->willReturn($page);
        $req
            ->method('getPerPage')
            ->willReturn($perPage);
        $req
            ->method('getDateFrom')
            ->willReturn($fromDate);
        $req
            ->method('getDateTo')
            ->willReturn($toDate);
        $req
            ->method('getQuery')
            ->willReturn($query);

        $criteria = ListEntriesCriteria::fromRequest($req);

        $actualPage = $criteria->getPage();
        $this->assertSame($page, $actualPage);

        $actualPerPage = $criteria->getPerPage();
        $this->assertSame($perPage, $actualPerPage);

        $actualFrom = $criteria->getFromDate();
        $this->assertSame($fromDate, $actualFrom);

        $actualTo = $criteria->getToDate();
        $this->assertSame($toDate, $actualTo);

        $actualQuery = $criteria->getQuery();
        $this->assertSame($query, $actualQuery);
    }

    /**
     * AC-2: Defaults are applied when request has empty/zero values.
     *
     * Mechanics:
     *  - page default -> 1
     *  - perPage default -> 10 (tunable)
     *  - empty dates ('') -> null
     *  - null dates -> null
     *  - query '' or "   " -> null (after trim)
     */
    public function testDefaultsAndNormalizationAreApplied(): void
    {
        $page     = 0;
        $perPage  = 0;
        $fromDate = '';
        $toDate   = null;
        $query    = '   ';

        $reqClass = ListEntriesRequestInterface::class;
        $req      = $this->createMock($reqClass);
        $req
            ->method('getPage')
            ->willReturn($page);
        $req
            ->method('getPerPage')
            ->willReturn($perPage);
        $req
            ->method('getDateFrom')
            ->willReturn($fromDate);
        $req
            ->method('getDateTo')
            ->willReturn($toDate);
        $req
            ->method('getQuery')
            ->willReturn($query);

        $criteria = ListEntriesCriteria::fromRequest($req);

        $actualPage = $criteria->getPage();
        $this->assertSame(1, $actualPage);

        $actualPerPage = $criteria->getPerPage();
        $this->assertSame(10, $actualPerPage);

        $actualFrom = $criteria->getFromDate();
        $this->assertNull($actualFrom);

        $actualTo = $criteria->getToDate();
        $this->assertNull($actualTo);

        $actualQuery = $criteria->getQuery();
        $this->assertNull($actualQuery);
    }

    /**
     * AF-1: Query is trimmed but not otherwise transformed.
     *
     * Cases:
     *  - leading/trailing spaces are removed;
     *  - inner spaces preserved;
     *  - non-empty after trim remains as-is.
     */
    public function testQueryIsTrimmed(): void
    {
        $query = '  project alpha  beta  ';

        $reqClass = ListEntriesRequestInterface::class;
        $req      = $this->createMock($reqClass);
        $req
            ->method('getPage')
            ->willReturn(1);
        $req
            ->method('getPerPage')
            ->willReturn(10);
        $req
            ->method('getDateFrom')
            ->willReturn(null);
        $req
            ->method('getDateTo')
            ->willReturn(null);
        $req
            ->method('getQuery')
            ->willReturn($query);

        $criteria = ListEntriesCriteria::fromRequest($req);

        $actualQuery = $criteria->getQuery();
        $this->assertSame('project alpha  beta', $actualQuery);
    }

    /**
     * AC-3: Sorting strategy is fixed to business default.
     *
     * Mechanics:
     *  - Primary: date DESC;
     *  - Secondary (stable): createdAt DESC.
     * The criteria exposes a read-only descriptor for repositories.
     */
    public function testSortDescriptorIsFixed(): void
    {
        $reqClass = ListEntriesRequestInterface::class;
        $req      = $this->createMock($reqClass);
        $req
            ->method('getPage')
            ->willReturn(1);
        $req
            ->method('getPerPage')
            ->willReturn(10);
        $req
            ->method('getDateFrom')
            ->willReturn(null);
        $req
            ->method('getDateTo')
            ->willReturn(null);
        $req
            ->method('getQuery')
            ->willReturn(null);

        $criteria = ListEntriesCriteria::fromRequest($req);

        $sort = $criteria->getSortDescriptor();
        $this->assertSame(
            [
                ['field' => 'date',      'direction' => 'DESC'],
                ['field' => 'createdAt', 'direction' => 'DESC'],
            ],
            $sort
        );
    }
}
