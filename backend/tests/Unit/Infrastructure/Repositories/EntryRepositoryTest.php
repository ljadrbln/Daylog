<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Repositories;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Infrastructure\Repositories\Entries\EntryRepository;
use Daylog\Tests\Support\Fakes\FakeEntryStorage;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Models\Entries\ListEntriesCriteria;

use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Domain\Models\Entries\ListEntriesConstraints;
use Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer;
use Daylog\Presentation\Requests\Entries\ListEntriesSanitizer;


/**
 * Class EntryRepositoryTest
 *
 * Verifies that repository delegates to storage and returns UUID.
 */
final class EntryRepositoryTest extends Unit
{
    /**
     * Unit test: EntryRepository::save()
     *
     * Purpose:
     * Verify that repository delegates persistence to storage and returns a domain Entry,
     * not an array payload.
     *
     * Mechanics:
     * - Use FakeEntryStorage to simulate persistence.
     * - storage->insert() returns predefined UUID and records the inserted Entry instance.
     * - Repository returns an Entry with id/title/body/date/createdAt/updatedAt filled.
     *
     * Assertions:
     * - Result is an Entry instance.
     * - Result id equals FakeEntryStorage::returnUuid.
     * - Result title/body/date equal to the original data.
     * - Storage insert is called exactly once.
     * - Storage lastInserted is the same Entry instance that we passed in.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Repositories\Entries\EntryRepository::save
     */
    public function testSaveDelegatesToStorageAndReturnsPayload(): void
    {
        // Arrange 
        $storage = new FakeEntryStorage();
        $repo    = new EntryRepository($storage);

        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        // Act
        $result = $repo->save($entry);

        // Assert
        $id        = $result->getId();
        $title     = $result->getTitle();
        $body      = $result->getBody();
        $date      = $result->getDate();
        $createdAt = $result->getCreatedAt();
        $updatedAt = $result->getUpdatedAt();

        $this->assertSame($storage->returnUuid, $id);
        $this->assertSame($data['title'],       $title);
        $this->assertSame($data['body'],        $body);
        $this->assertSame($data['date'],        $date);

        $this->assertNotEmpty($createdAt);
        $this->assertNotEmpty($updatedAt);

        $this->assertSame(1, $storage->insertCalls);
        $this->assertSame($entry, $storage->lastInserted);
    }

    /**
     * Unit test: EntryRepository::findById() — happy path
     *
     * Purpose:
     * Ensure repository delegates lookup to storage and returns a hydrated Entry when id exists.
     *
     * Mechanics:
     * - Persist an Entry via FakeEntryStorage->insert() to seed in-memory state.
     * - Call repository->findById($id).
     * - Verify returned Entry fields.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Repositories\Entries\EntryRepository::findById
     */
    public function testFindByIdDelegatesToStorageAndReturnsEntry(): void
    {
        // Arrange
        $storage = new FakeEntryStorage();
        $repo    = new EntryRepository($storage);

        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        // seed fake storage
        $storage->insert($entry);

        $id = $entry->getId();

        // Act
        $result = $repo->findById($id);

        // Assert
        $this->assertInstanceOf(Entry::class, $result);

        $resultId    = $result->getId();
        $resultTitle = $result->getTitle();
        $resultBody  = $result->getBody();
        $resultDate  = $result->getDate();

        $this->assertSame($id,              $resultId);
        $this->assertSame($data['title'],   $resultTitle);
        $this->assertSame($data['body'],    $resultBody);
        $this->assertSame($data['date'],    $resultDate);
    }

    /**
     * Unit test: EntryRepository::findById() — not found
     *
     * Purpose:
     * Ensure repository returns null when storage cannot find an entry by id.
     *
     * Mechanics:
     * - Use a fresh FakeEntryStorage without seeding.
     * - Call repository->findById($id) with a random UUID.
     * - Verify null result.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Repositories\Entries\EntryRepository::findById
     */
    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        // Arrange
        $storage = new FakeEntryStorage();
        $repo    = new EntryRepository($storage);

        $missingId = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';

        // Act
        $result = $repo->findById($missingId);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Unit test: EntryRepository::findByCriteria() — UC-2 pass-through
     *
     * Purpose:
     * Verify repository delegates paging/filtering to storage and returns the expected page shape.
     * We do not test filtering/sorting here — only delegation and structure.
     *
     * Mechanics:
     * - FakeEntryStorage::findByCriteria() always returns an empty page with defaults.
     * - Call repository->findByCriteria($criteria).
     * - Assert stable shape: items/total/page/perPage/pagesCount.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Repositories\Entries\EntryRepository::findByCriteria
     */
    public function testFindByCriteriaDelegatesToStorageAndReturnsPageArray(): void
    {
        // Arrange
        $storage  = new FakeEntryStorage();
        $repo     = new EntryRepository($storage);

        $data = ListEntriesHelper::getData();
        $data = ListEntriesSanitizer::sanitize($data);

        $request  = ListEntriesHelper::buildRequest($data);       
        $params   = ListEntriesInputNormalizer::normalize($request);
        $criteria = ListEntriesCriteria::fromArray($params);
        
        // Act
        $pageResult = $repo->findByCriteria($criteria);

        // Assert
        $this->assertIsArray($pageResult);

        $items      = $pageResult['items']      ?? null;
        $total      = $pageResult['total']      ?? null;
        $pageNum    = $pageResult['page']       ?? null;
        $limit      = $pageResult['perPage']    ?? null;
        $pagesCount = $pageResult['pagesCount'] ?? null;

        $this->assertIsArray($items);
        $this->assertSame(0, $total);
        $this->assertSame(1, $pageNum);
        $this->assertSame(20, $limit);
        $this->assertSame(0, $pagesCount);
    }    
}

    // public function testQueryIsPreservedAfterUpstreamTrim(): void
    // {
    //     // Arrange
    //     $expectedQuery = '  project alpha  beta  ';
    //     $filters = [
    //         'query' => $expectedQuery
    //     ];

    //     $base    = ListEntriesHelper::getData();
    //     $data    = ListEntriesHelper::withFilters($base, $filters);
    //     $data    = ListEntriesSanitizer::sanitize($data);

    //     $request = ListEntriesHelper::buildRequest($data);       
    //     $params  = ListEntriesInputNormalizer::normalize($request);

    //     // Act
    //     $criteria = ListEntriesCriteria::fromArray($params);

    //     // Assert
    //     $expectedQuery = trim($expectedQuery);
    //     $actualQuery   = $criteria->getQuery();
    //     $this->assertSame($expectedQuery, $actualQuery);
    // }