<?php

namespace Daylog\Tests\Unit\Application\DTO\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesCriteria;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Domain\Models\Entries\ListEntriesConstraints;

/**
 * Tests for ListEntriesCriteria mapping and normalization.
 *
 * This suite verifies that the criteria:
 *  - maps request fields 1:1 without business validation;
 *  - applies defaults for page/perPage;
 *  - trims and normalizes query (empty -> null);
 *  - converts empty date strings to null, leaving valid YYYY-MM-DD as-is.
 *
 * @covers \Daylog\Application\DTO\Entries\ListEntriesCriteria
 */
final class ListEntriesCriteriaTest extends Unit
{
    /**
     * Given a valid request, returns a criteria with mapped values.
     *
     * Data source: a stub of ListEntriesRequestInterface.
     * We check the getters for exact values without additional validation.
     */
    public function testFromRequestReturnsMappedValues(): void
    {
        /** Arrange **/
        $filters = [
            'dateFrom' => '2025-08-01',
            'dateTo'   => '2025-08-21',
            'date'     => '2025-08-15',
            'query'    => 'notes',
        ];

        $base = ListEntriesHelper::getData();
        $data = ListEntriesHelper::withFilters($base, $filters);

        $request = ListEntriesHelper::buildRequest($data);

        /** Act **/
        $criteria = ListEntriesCriteria::fromRequest($request);

        /** Assert **/
        $actualPage = $criteria->getPage();
        $this->assertSame($base['page'], $actualPage);

        $actualPerPage = $criteria->getPerPage();
        $this->assertSame($base['perPage'], $actualPerPage);

        $actualFrom = $criteria->getDateFrom();
        $this->assertSame($filters['dateFrom'], $actualFrom);

        $actualTo = $criteria->getDateTo();
        $this->assertSame($filters['dateTo'], $actualTo);

        $actualDate = $criteria->getDate();
        $this->assertSame($filters['date'], $actualDate);

        $actualQuery = $criteria->getQuery();
        $this->assertSame($filters['query'], $actualQuery);

        $sortDescriptor = $criteria->getSortDescriptor();
        $expectedSort   = [
            ['field' => $base['sortField'], 'direction' => $base['sortDir']],
            ['field' => 'createdAt',        'direction' => 'DESC'],
        ];
        $this->assertSame($expectedSort, $sortDescriptor);
    }

    /**
     * Defaults are applied when request has empty/zero values.
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
        // invalid            
        $page    = 0;
        $perPage = 0;

        $filters = [
            'dateFrom' => '',      // empty string → null
            'dateTo'   => null,    // null → null
            'query'    => '   ',   // only spaces → null
        ];

        $base = ListEntriesHelper::getData($page, $perPage);
        $data = ListEntriesHelper::withFilters($base, $filters);

        $request  = ListEntriesHelper::buildRequest($data);
        $criteria = ListEntriesCriteria::fromRequest($request);

        $expectedPage = ListEntriesConstraints::PAGE_MIN;
        $actualPage   = $criteria->getPage();
        $this->assertSame($expectedPage, $actualPage);

        $expectedPerPage = ListEntriesConstraints::PER_PAGE_DEFAULT;
        $actualPerPage   = $criteria->getPerPage();
        $this->assertSame($expectedPerPage, $actualPerPage);

        $this->assertNull($criteria->getDateFrom());
        $this->assertNull($criteria->getDateTo());
        $this->assertNull($criteria->getQuery());
    }    

    /**
     * Query is trimmed but not otherwise transformed.
     *
     * Cases:
     *  - leading/trailing spaces are removed;
     *  - inner spaces preserved;
     *  - non-empty after trim remains as-is.
     */
    public function testQueryIsTrimmed(): void
    {
        $filters = [
            'query' => '  project alpha  beta  '
        ];

        $base = ListEntriesHelper::getData();
        $data = ListEntriesHelper::withFilters($base, $filters);
        $request = ListEntriesHelper::buildRequest($data);

        $criteria = ListEntriesCriteria::fromRequest($request);

        $actualQuery = $criteria->getQuery();
        $this->assertSame('project alpha  beta', $actualQuery);
    }

    /**
     * Sorting strategy is fixed to business default.
     *
     * Mechanics:
     *  - Primary: date DESC;
     *  - Secondary (stable): createdAt DESC.
     * The criteria exposes a read-only descriptor for repositories.
     */
    public function testSortDescriptorIsFixed(): void
    {
        $data = ListEntriesHelper::getData();
        
        $request  = ListEntriesHelper::buildRequest($data);
        $criteria = ListEntriesCriteria::fromRequest($request);

        $sort = $criteria->getSortDescriptor();
        $this->assertSame(ListEntriesConstraints::SORT_DESCRIPTOR, $sort);
    }
}
