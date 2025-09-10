<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Codeception\Test\Unit;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Configuration\Providers\Entries\UpdateEntryProvider;
use Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntryInterface;
use Daylog\Tests\Support\Fixture\EntryFixture;
use DB\SQL;

/**
 * Base class for UC-5 UpdateEntry integration tests.
 *
 * Purpose:
 *   Provide a shared, real-wired environment for UpdateEntry tests:
 *   a clean DB connection, registered fixtures, and a ready-to-use use case instance.
 *
 * Mechanics:
 *   - Obtain DB via SqlFactory (single source of truth).
 *   - Clean the 'entries' table before each test to ensure isolation.
 *   - Register DB in EntryFixture for convenience.
 *   - Build the use case via configuration provider.
 *
 * @internal Extend this class in AC/AF test files (AC1_..., AF1_..., etc.).
 */
abstract class BaseUpdateEntryIntegrationTest extends Unit
{
    /** @var SQL */
    protected SQL $db;

    /**
     * Use case instance wired via configuration provider.
     *
     * @var UpdateEntryInterface
     */
    protected UpdateEntryInterface $useCase;

    /**
     * Prepare shared DB and wire the use case.
     *
     * @return void
     */
    protected function _before(): void
    {
        // Register DB in fixtures and ensure a clean table
        $db = SqlFactory::get();
        EntryFixture::setDb($db);
        EntryFixture::cleanTable();

        // Use case via configuration provider (wiring must exist)
        $useCase = UpdateEntryProvider::useCase();

        $this->db      = $db;
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
