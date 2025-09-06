<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\DeleteEntry;

use Codeception\Test\Unit;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Configuration\Providers\Entries\DeleteEntryProvider;
use Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntryInterface;
use Daylog\Tests\Support\Fixture\EntryFixture;
use DB\SQL;

/**
 * Base class for UC-4 DeleteEntry integration tests.
 *
 * Purpose:
 * Provide a shared, real-wired environment for DeleteEntry tests:
 * a clean DB connection, registered fixtures, and a ready-to-use use case instance.
 *
 * Mechanics:
 * - Obtain DB via SqlFactory (single source of truth).
 * - Clean the 'entries' table before each test to ensure isolation.
 * - Register DB in EntryFixture for convenience.
 * - Build the use case via configuration provider.
 * 
 * @internal Extend this class in AC/AF test files (AC1_..., AF1_..., etc.).
 */
abstract class BaseDeleteEntryIntegrationTest extends Unit
{
    /**
     * Real DB connection used by child tests.
     *
     * @var SQL
     */
    protected SQL $db;

    /**
     * Use case instance wired via configuration provider.
     *
     * @var DeleteEntryInterface
     */
    protected DeleteEntryInterface $useCase;

    /**
     * Prepare shared DB and wire the use case.
     *
     * @return void
     */
    protected function _before(): void
    {
        // DB from the single source of truth
        $db = SqlFactory::get();

        // Store for child tests
        $this->db = $db;

        // Clean table and register in fixtures
        EntryFixture::setDb($db);
        EntryFixture::cleanTable();

        // Wire use case via provider
        $useCase = DeleteEntryProvider::useCase();

        $this->useCase = $useCase;
    }

    /**
     * Ensure DB table is clean after each test.
     *
     * @return void
     */
    protected function _after(): void
    {
        EntryFixture::cleanTable();
    }
}
