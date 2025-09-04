<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Codeception\Test\Unit;

use Daylog\Configuration\Providers\Entries\ListEntriesProvider;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Tests\Support\Fixture\EntryFixture;

/**
 * Base class for UC-2 ListEntries integration tests.
 *
 * Purpose:
 *   Provide a shared, real-wired environment for ListEntries tests:
 *   a clean DB connection, registered fixtures, and a ready-to-use use case instance.
 *
 * Mechanics:
 *   - Obtain DB via SqlFactory (single source of truth).
 *   - Truncate the 'entries' table before each test to ensure isolation.
 *   - Register DB in EntryFixture for convenience.
 *   - Build the use case via configuration provider.
 *
 * @internal Extend this class in AC/AF test files (AC1_..., AC2_..., etc.).
 */
abstract class BaseListEntriesIntegrationTest extends Unit
{
    /** @var \DB\SQL */
    protected $db;

    /** @var \Daylog\Application\UseCases\Entries\ListEntries */
    protected $useCase;

    /**
     * Prepare shared DB and wire the use case.
     *
     * @return void
     */
    protected function _before(): void
    {
        // Register DB in fixtures
        $db = SqlFactory::get();
        EntryFixture::setDb($db);
        EntryFixture::cleanTable();

        // Use case
        $provider = ListEntriesProvider::class;
        $useCase  = $provider::useCase();

        $this->useCase = $useCase;
    }

    /**
     * Ensure DB table is clean after each test and release overrides.
     *
     * @return void
     */
    protected function _after(): void
    {
        EntryFixture::cleanTable();
    }    
}
