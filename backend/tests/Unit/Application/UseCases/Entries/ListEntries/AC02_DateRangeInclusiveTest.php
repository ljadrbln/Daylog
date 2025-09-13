<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Models\Entries\Entry;

/**
 * UC-2 / AC-2 — Date range inclusive — Unit.
 *
 * Purpose:
 * Ensure entries equal to dateFrom/dateTo bounds are included and ordering is date DESC.
 *
 * Mechanics:
 * - Seed fake repo with three entries dated 2025-08-10..12.
 * - Build request via ListEntriesTestRequestFactory::rangeInclusive('2025-08-10','2025-08-11').
 * - Expect exactly two results: 2025-08-11, then 2025-08-10.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC02_DateRangeInclusiveTest extends BaseListEntriesUnitTest
{
    /**
     * Inclusive [dateFrom..dateTo] returns boundary items ordered by date DESC.
     *
     * @return void
     */
    public function testDateRangeInclusiveReturnsMatchingItems(): void
    {
        // Arrange
        $repo = $this->makeRepo();

        $d1 = '2025-08-10';
        $d2 = '2025-08-11';
        $d3 = '2025-08-12';

        $data1  = EntryTestData::getOne('Valid title', 'Valid body', $d1);
        $entry1 = Entry::fromArray($data1);
        $repo->save($entry1);

        $data2  = EntryTestData::getOne('Valid title', 'Valid body', $d2);
        $entry2 = Entry::fromArray($data2);
        $repo->save($entry2);

        $data3  = EntryTestData::getOne('Valid title', 'Valid body', $d3);
        $entry3 = Entry::fromArray($data3);
        $repo->save($entry3);

        $request   = ListEntriesTestRequestFactory::rangeInclusive($d1, $d2);
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $response = $useCase->execute($request);
        $items    = $response->getItems();

        // Assert
        $this->assertCount(2, $items);
        $this->assertSame($d2, $items[0]['date']);
        $this->assertSame($d1, $items[1]['date']);
    }
}
