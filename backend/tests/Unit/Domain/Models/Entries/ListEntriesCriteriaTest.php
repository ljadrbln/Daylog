<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Domain\Models\Entries;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\ListEntriesCriteria;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Domain\Models\Entries\ListEntriesConstraints;
use Daylog\Application\Normalization\Entries\ListEntries\ListEntriesInputNormalizer;
use Daylog\Presentation\Requests\Entries\ListEntries\ListEntriesSanitizer;

/**
 * Unit tests for Domain ListEntriesCriteria (UC-2).
 *
 * Purpose:
 * Verify that the Domain Criteria acts as a thin immutable value object:
 * it copies already-normalized values from the Request DTO without re-normalization
 * and exposes a stable two-level sort descriptor (primary from request + secondary createdAt DESC).
 *
 * Mechanics:
 * - Data source: a stub implementing ListEntriesRequestInterface (via ListEntriesHelper).
 * - We assert 1:1 field mapping and fixed secondary sort only.
 *
 * @covers \Daylog\Domain\Models\Entries\ListEntriesCriteria
 */
final class ListEntriesCriteriaTest extends Unit
{
    /**
     * Given a valid, normalized request, Criteria maps fields 1:1.
     *
     * Data source:
     * - Base values from ListEntriesHelper::getData()
     * - Filters override a subset of fields
     *
     * We verify getters and the full sort descriptor.
     */
    public function testFromRequestReturnsMappedValues(): void
    {
        // Arrange
        $filters = [
            'dateFrom' => '2025-08-01',
            'dateTo'   => '2025-08-21',
            'date'     => '2025-08-15',
            'query'    => 'notes',
        ];

        $base = ListEntriesHelper::getData();
        $data = ListEntriesHelper::withFilters($base, $filters);
        $data = ListEntriesSanitizer::sanitize($data);

        $request = ListEntriesHelper::buildRequest($data);
        $params  = ListEntriesInputNormalizer::normalize($request);

        // Act
        $criteria = ListEntriesCriteria::fromArray($params);

        // Assert
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
            ['field' => $base['sortField'], 'direction' => $base['sortDir']], // primary from request
            ['field' => 'createdAt',        'direction' => 'DESC'],            // stable secondary
        ];
        $this->assertSame($expectedSort, $sortDescriptor);
    }

    /**
     * Defaults and normalization are preserved (but not performed here).
     *
     * Scenario:
     * - Request carries invalid/empty values which were already normalized upstream.
     * - Criteria must simply reflect those normalized values without changes.
     *
     * Checks:
     * - page: given < PAGE_MIN => clamped to PAGE_MIN
     * - perPage: given < PER_PAGE_MIN => clamped to PER_PAGE_MIN
     * - dates: empty/null => null
     * - query: spaces-only => null (after upstream trim)
     */
    public function testDefaultsAndNormalizationArePreservedFromRequest(): void
    {
        // Arrange
        $page    = 0; // invalid, upstream => PAGE_MIN
        $perPage = 0; // invalid, upstream => PER_PAGE_MIN (NOT DEFAULT)

        $filters = [
            'dateFrom' => '',
            'dateTo'   => null,
            'query'    => '   ',
        ];

        $base    = ListEntriesHelper::getData($page, $perPage);
        $data    = ListEntriesHelper::withFilters($base, $filters);
        $data    = ListEntriesSanitizer::sanitize($data);

        $request = ListEntriesHelper::buildRequest($data);
        $params  = ListEntriesInputNormalizer::normalize($request);

        // Act
        $criteria = ListEntriesCriteria::fromArray($params);

        // Assert
        $expectedPage = ListEntriesConstraints::PAGE_MIN;
        $this->assertSame($expectedPage, $criteria->getPage());

        $expectedPerPage = ListEntriesConstraints::PER_PAGE_MIN;
        $this->assertSame($expectedPerPage, $criteria->getPerPage());

        $this->assertNull($criteria->getDateFrom());
        $this->assertNull($criteria->getDateTo());
        $this->assertNull($criteria->getQuery());
    }

    /**
     * Query is trimmed upstream and preserved as-is by Criteria.
     *
     * Cases:
     * - Leading/trailing spaces are removed upstream.
     * - Inner spaces are preserved.
     * - Non-empty after trim remains intact.
     */
    public function testQueryIsPreservedAfterUpstreamTrim(): void
    {
        // Arrange
        $expectedQuery = '  project alpha  beta  ';
        $filters = [
            'query' => $expectedQuery
        ];

        $base    = ListEntriesHelper::getData();
        $data    = ListEntriesHelper::withFilters($base, $filters);
        $data    = ListEntriesSanitizer::sanitize($data);

        $request = ListEntriesHelper::buildRequest($data);       
        $params  = ListEntriesInputNormalizer::normalize($request);

        // Act
        $criteria = ListEntriesCriteria::fromArray($params);

        // Assert
        $expectedQuery = trim($expectedQuery);
        $actualQuery   = $criteria->getQuery();
        $this->assertSame($expectedQuery, $actualQuery);
    }

    /**
     * Sorting descriptor exposes primary from request + stable secondary (createdAt DESC).
     *
     * Mechanics:
     * - Primary comes directly from the normalized request (field + direction).
     * - Secondary is always fixed to createdAt DESC for stability.
     */
    public function testSortDescriptorPrimaryFromRequestAndSecondaryStable(): void
    {
        // Arrange
        $data     = ListEntriesHelper::getData();
        $request  = ListEntriesHelper::buildRequest($data);
        $params   = ListEntriesInputNormalizer::normalize($request);

        // Act
        $criteria = ListEntriesCriteria::fromArray($params);

        // Assert
        $sort = $criteria->getSortDescriptor();

        $expected = [
            ['field' => $data['sortField'], 'direction' => $data['sortDir']],
            ['field' => 'createdAt',        'direction' => 'DESC'],
        ];

        $this->assertSame($expected, $sort);
    }
}
