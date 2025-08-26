<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Infrastructure\Storage\Entry;

use Codeception\Test\Unit;
use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Infrastructure\Utils\Variables;
use Daylog\Infrastructure\Utils\DSNParser;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;
use DB\SQL;

use Daylog\Tests\Support\Helper\EntryTestData;

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


        /** @var array<string,string> $data */
        $data = EntryTestData::getOne();
        $data = EntryFieldMapper::toDbRow($data);
        $uuid = $data['id'];

        $model->create($data);

        /** @var array<string,mixed>|null $fetched */
        $fetched = $model->getRowByUuid($uuid);

        $this->assertNotNull($fetched);
        $this->assertSame($data['title'], $fetched['title']);
        $this->assertSame($data['body'],  $fetched['body']);
        $this->assertSame($data['date'],  $fetched['date']);
    }    
}



