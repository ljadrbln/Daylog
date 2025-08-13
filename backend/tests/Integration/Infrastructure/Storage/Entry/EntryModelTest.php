<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Infrastructure\Storage\Entry;

use Codeception\Test\Unit;
use Daylog\Infrastructure\Storage\Entry\EntryModel;
use Daylog\Infrastructure\Utils\Variables;
use Daylog\Infrastructure\Utils\DSNParser;
use DB\SQL;

/**
 * Class EntryModelTest
 *
 * Integration test for EntryModel using a real database connection.
 * Database state is prepared by Codeception Db module with dump.sql.
 */
final class EntryModelTest extends Unit
{
    /**
     * @var SQL Database connection
     */
    private SQL $db;

    /**
     * Set up the database connection before each test.
     *
     * @return void
     */
    protected function _before(): void
    {
        $dsnString = Variables::getDB();
        [$dsn, $user, $pass] = DSNParser::parse($dsnString);

        /** @var SQL $db */
        $db = new SQL($dsn, $user, $pass);
        $this->db = $db;
    }

    /**
     * Test inserting an entry and fetching it back.
     *
     * This test is expected to fail (Red) because insert() and getById()
     * methods are not yet implemented in EntryModel.
     *
     * @return void
     */
    public function testInsertAndFetch(): void
    {
        /** @var EntryModel $model */
        $model = new EntryModel($this->db);

        $id        = '123e4567-e89b-12d3-a456-426614174000';
        $title     = 'Test title';
        $body      = 'Test body';
        $date      = '2025-08-13';
        $createdAt = '2025-08-13 12:00:00';
        $updatedAt = '2025-08-13 12:00:00';

        $model->insert($id, $title, $body, $date, $createdAt, $updatedAt);
        $fetched = $model->getById($id);

        $this->assertNotNull($fetched);
        $this->assertSame($title, $fetched['title']);
        $this->assertSame($body, $fetched['body']);
        $this->assertSame($date, $fetched['date']);
    }
}
