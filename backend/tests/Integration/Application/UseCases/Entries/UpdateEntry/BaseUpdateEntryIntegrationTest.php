<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Codeception\Test\Unit;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Configuration\Providers\Entries\UpdateEntryProvider;
use Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntryInterface;
use Daylog\Tests\Support\Fixture\EntryFixture;
use DB\SQL;

use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;
use Daylog\Domain\Services\Clock;
use Daylog\Domain\Models\Entries\Entry;
use DateTimeImmutable;

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

    /**
     * Insert a single Entry via real model with both timestamps shifted into the past.
     *
     * Purpose:
     * - Ensure BR-2 holds (updatedAt ≥ createdAt) while guaranteeing that
     *   subsequent UC execution (with Clock::now()) produces a strictly newer updatedAt.
     *
     * Mechanics:
     * - Take Clock::now(), shift by $shiftSpec (default "-1 hour").
     * - Build domain Entry from EntryTestData with createdAt/updatedAt = shifted ISO.
     * - Map to DB row and persist through EntryModel.
     *
     * @param string $shiftSpec Relative time spec for DateTimeImmutable::modify(), e.g. "-1 hour".
     * @param array<string,string> $overrides Optional field overrides: title, body, date, createdAt, updatedAt.
     * @return array{
     *   id:string,
     *   title:string,
     *   body:string,
     *   date:string,
     *   createdAt:string,
     *   updatedAt:string
     * } Inserted entry payload (ISO-8601 UTC in timestamps).
     */
    protected function insertEntryWithPastTimestamps(string $shiftSpec = '-1 hour', array $overrides = []): array
    {
        // Baseline "now" in UTC (ISO-8601)
        $nowIso = Clock::now();
        $nowObj = new DateTimeImmutable($nowIso);

        // Shift both timestamps into the past
        $pastObj = $nowObj->modify($shiftSpec);
        $pastIso = $pastObj->format(DATE_ATOM);

        // Defaults (can be overridden)
        $title = $overrides['title'] ?? 'Valid title';
        $body  = $overrides['body']  ?? 'Valid body';
        $date  = $overrides['date']  ?? '2025-09-10';

        $createdAt = $overrides['createdAt'] ?? $pastIso;
        $updatedAt = $overrides['updatedAt'] ?? $pastIso;

        // Build domain entry from canonical test data
        $data  = EntryTestData::getOne($title, $body, $date, $createdAt, $updatedAt);
        $entry = Entry::fromArray($data);

        // Map to DB row and persist via real model
        $dbRow = EntryFieldMapper::toDbRowFromEntry($entry);

        $model = new EntryModel($this->db);
        $model->createEntry($dbRow);

        return $data;
    }    
}
