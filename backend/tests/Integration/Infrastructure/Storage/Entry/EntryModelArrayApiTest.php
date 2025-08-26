<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Infrastructure\Storage\Entries;

use Codeception\Test\Unit;
use DB\SQL;
use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Tests\Support\Fixture\EntryFixture;

/**
 * Integration test for EntryModel array API.
 *
 * Purpose:
 * - Verify that findRows() returns plain arrays with DB field names (snake_case).
 * - Verify that countRows() returns an integer for the same filter.
 *
 * Mechanics:
 * - Seeds real DB via EntryFixture (shared SQL set in _before()).
 * - Uses F3 Mapper through the real EntryModel (infrastructure boundary).
 *
 * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel
 */
final class EntryModelArrayApiTest extends Unit
{
    /** @var SQL */
    private SQL $db;

    /**
     * Prepare shared DB and clean state.
     *
     * @return void
     */
    protected function _before(): void
    {
        $db = SqlFactory::get();
        EntryFixture::setDb($db);

        $sql = 'DELETE FROM entries';
        $db->exec($sql);
    }

    /**
     * Ensure findRows() returns list<array> with snake_case keys.
     *
     * Scenarios:
     * - No filters beyond date range, default order + paging provided.
     * - Check array shape: id, date, title, body, created_at, updated_at.
     *
     * @return void
     */
    public function testFindRowsReturnsPlainArraysWithSnakeCase(): void
    {
        EntryFixture::insertRows(3, 1);

        $db    = EntryFixture::getDb();
        $model = new EntryModel($db);

        $filter  = ['date >= ? AND date <= ?', '2025-01-01', '2025-12-31'];
        $options = ['order' => 'date DESC, created_at DESC', 'limit' => 10, 'offset' => 0];

        $rows = $model->findRows($filter, $options);

        $this->assertIsArray($rows);
        $this->assertNotEmpty($rows);

        $first = $rows[0];

        $this->assertIsArray($first);
        $this->assertArrayHasKey('id', $first);
        $this->assertArrayHasKey('date', $first);
        $this->assertArrayHasKey('title', $first);
        $this->assertArrayHasKey('body', $first);
        $this->assertArrayHasKey('created_at', $first);
        $this->assertArrayHasKey('updated_at', $first);
    }

    /**
     * Ensure countRows() returns integer for a given filter.
     *
     * Scenarios:
     * - Seed fixed number of rows and check that result is non-negative integer.
     *
     * @return void
     */
    public function testCountRowsMatchesFilter(): void
    {
        EntryFixture::insertRows(2, 1);

        $db    = EntryFixture::getDb();
        $model = new EntryModel($db);

        $filter = ['title LIKE ?', '%Title%'];

        $total = $model->countRows($filter);

        $this->assertIsInt($total);
        $this->assertGreaterThanOrEqual(0, $total);
    }
}
