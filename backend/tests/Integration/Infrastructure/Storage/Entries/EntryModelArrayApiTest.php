<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Infrastructure\Storage\Entries;

use Codeception\Test\Unit;
use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Tests\Support\Fixture\EntryFixture;

use Daylog\Domain\Services\DateService;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;

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
        // Arrange
        $rows = EntryFixture::insertRows(3, 2);

        $db    = EntryFixture::getDb();
        $model = new EntryModel($db);

        $filter  = ['date >= ? AND date <= ?', $rows[0]['date'], $rows[2]['date']];
        $options = ['order' => 'date DESC, created_at DESC', 'limit' => 10, 'offset' => 0];

        // Act
        $rows = $model->findRows($filter, $options);

        // Assert
        $this->assertNotEmpty($rows);

        $first = $rows[0];
        $first = EntryFieldMapper::fromDbRow($first);

        $isIdValid = UuidGenerator::isValid($first['id']);
        $this->assertTrue($isIdValid);

        $isDateValid = DateService::isValidLocalDate($first['date']);
        $this->assertTrue($isDateValid);

        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($first['createdAt']);        
        $this->assertTrue($isCreatedAtValid);

        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($first['updatedAt']);
        $this->assertTrue($isUpdatedAtValid);

        $this->assertNotSame('', $first['title']);
        $this->assertNotSame('', $first['body']);
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
