<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

/**
 * AC-5: Sorting by createdAt/updatedAt supports ASC/DESC.
 *
 * Purpose: 
 *   - Verify that the 'sort' parameter affects ordering for both timestamp fields in both directions using real DB wiring.
 * 
 * Mechanics: 
 *   - Seed 3 rows with same logical date; 
 *   - set distinct created_at/updated_at for clear ASC/DESC; 
 *   - run via data provider; 
 *   - resolve id markers to real UUIDs before asserting.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 * 
 * @group UC-ListEntries
 */
final class AC5_SortingByFieldsTest extends BaseListEntriesIntegrationTest
{   
    /** 
     * AC-5: Sort by createdAt/updatedAt ASC|DESC via data provider; markers resolved to real UUIDs. 
     * 
     * @dataProvider provideSortingScenarios 
     * 
     * @param string $sortField 
     * @param 'ASC'|'DESC' $sortDir 
     * @param array<int,string> $expectedMarkers 
     * 
     * @return void  
     */
    public function testSortingByCreatedAtAndUpdatedAtAscDesc(
        string $sortField,
        string $sortDir,
        array $expectedMarkers
    ): void {
        // Arrange: seed 3 rows with the same logical date
        $rows = EntryFixture::insertRows(3, 0);

        $id1 = $rows[0]['id'];
        $c1  = '2025-08-12 10:00:00';
        $u1  = '2025-08-12 10:05:00';

        $id2 = $rows[1]['id'];
        $c2  = '2025-08-12 11:00:00';
        $u2  = '2025-08-12 11:05:00';

        $id3 = $rows[2]['id'];
        $c3  = '2025-08-12 12:00:00';
        $u3  = '2025-08-12 12:05:00';

        EntryFixture::updateById($id1, ['created_at' => $c1, 'updated_at' => $u1]);
        EntryFixture::updateById($id2, ['created_at' => $c2, 'updated_at' => $u2]);
        EntryFixture::updateById($id3, ['created_at' => $c3, 'updated_at' => $u3]);

        // Replace expected order markers with real UUIDs
        $expected = $this->resolveExpectedOrder($expectedMarkers, $id1, $id2, $id3);

        // Act
        $data          = ListEntriesHelper::getData();
        $field         = $sortField;
        $dir           = $sortDir;
        $data['sortField'] = $field;
        $data['sortDir']   = $dir;

        $req   = ListEntriesHelper::buildRequest($data);
        $res   = $this->useCase->execute($req);
        $items = $res->getItems();

        // Assert
        $this->assertSame($expected[0], $items[0]['id']);
        $this->assertSame($expected[1], $items[1]['id']);
        $this->assertSame($expected[2], $items[2]['id']);
    }

    /**
     * Provide scenarios for sorting by createdAt/updatedAt ASC/DESC.
     *
     * Each case returns [field, direction, expected order markers].
     * Markers are symbolic ('id1','id2','id3') and are resolved to real UUIDs
     * inside the test to avoid coupling the provider to runtime-generated values.
     *
     * @return array<string, array{0:string,1:'ASC'|'DESC',2:array<int,string>}>
     */
    public function provideSortingScenarios(): array
    {
        $cases = [
            'createdAt ASC returns oldest first'            => ['createdAt', 'ASC',  ['id1', 'id2', 'id3']],
            'createdAt DESC returns newest first'           => ['createdAt', 'DESC', ['id3', 'id2', 'id1']],
            'updatedAt ASC returns earliest updates first'  => ['updatedAt', 'ASC',  ['id1', 'id2', 'id3']],
            'updatedAt DESC returns latest updates first'   => ['updatedAt', 'DESC', ['id3', 'id2', 'id1']],

            'sort by date ASC returns oldest date first'    => ['date', 'ASC',  ['id1','id2','id3']],
        ];

        return $cases;
    }

    /**
     * Resolve expected order markers to real UUIDs generated at runtime.
     *
     * Scenario:
     *  - Input markers come from dataProvider ('id1','id2','id3').
     *  - This method maps them to actual UUIDs produced by EntryFixture::insertRows().
     *
     * Cases:
     *  - 'id1' => $id1
     *  - 'id2' => $id2
     *  - 'id3' => $id3
     *
     * @param array<int,string> $markers List of 'id1'|'id2'|'id3'.
     * @param string            $id1     UUID of the first seeded row.
     * @param string            $id2     UUID of the second seeded row.
     * @param string            $id3     UUID of the third seeded row.
     * @return array<int,string>         Expected UUID order for assertions.
     */
    private function resolveExpectedOrder(array $markers, string $id1, string $id2, string $id3): array
    {
        $map = [
            'id1' => $id1,
            'id2' => $id2,
            'id3' => $id3,
        ];

        $expected = [];
        for ($i = 0; $i < count($markers); $i++) {
            $marker   = $markers[$i];
            $expected[] = $map[$marker];
        }

        return $expected;
    }
}
