<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use DB\SQL;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Configuration\Providers\Entries\AddEntryProvider;
use Daylog\Application\UseCases\Entries\AddEntry\AddEntryInterface;
use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Integration\Application\UseCases\Entries\BaseEntryUseCaseIntegrationTest;

/**
 * Base class for UC-1 AddEntry integration tests.
 *
 * Purpose:
 *   Provide a shared, real-wired environment for AddEntry tests:
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
abstract class BaseAddEntryIntegrationTest extends BaseEntryUseCaseIntegrationTest
{
    /** @var SQL */
    protected SQL $db;

    /**
     * Use case instance wired via configuration provider.
     *
     * @var AddEntryInterface
     */
    protected AddEntryInterface $useCase;

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
        $useCase  = AddEntryProvider::useCase();

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
