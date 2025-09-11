<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Domain\Models\Entries;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit test for the Entry domain model (creation path).
 *
 * Purpose: verify that Entry exposes id/title/body/date and enforces BR-4 on creation:
 * createdAt and updatedAt must be set to the same snapshot time.
 * Mechanics: we reuse EntryTestData to get a valid (title/body/date) payload, prepare a fixed uuid
 * and a deterministic "now" derived from the logical date (00:00:00), then call the factory and assert getters.
 *
 * @covers \Daylog\Domain\Models\Entries\Entry
 */
final class EntryTest extends Unit
{
    /**
     * Verify Entry creation path invariants.
     *
     * Scenario:
     * - Use EntryTestData to provide a valid baseline payload.
     * - Construct Entry via fromArray() with deterministic values.
     * - Assert that getters return exactly the provided fields (id, title, body, date).
     * - Assert BR-4/BR-2 invariants: createdAt and updatedAt are equal,
     *   representing a single snapshot time on creation.
     *
     * @return void
     * @covers \Daylog\Domain\Models\Entries\Entry::fromArray
     */
    public function testCreateSetsEqualTimestampsAndExposesAllFields(): void
    {
        // Arrange
        $data = EntryTestData::getOne();

        // Act
        $entry = Entry::fromArray($data);

        // Assert: basic getters reflect inputs
        $actualId    = $entry->getId();
        $actualTitle = $entry->getTitle();
        $actualBody  = $entry->getBody();
        $actualDate  = $entry->getDate();

        $this->assertSame($data['id'],    $actualId);
        $this->assertSame($data['title'], $actualTitle);
        $this->assertSame($data['body'],  $actualBody);
        $this->assertSame($data['date'],  $actualDate);

        // Assert: BR-4 single snapshot on creation (createdAt == updatedAt == now)
        $createdAt = $entry->getCreatedAt();
        $updatedAt = $entry->getUpdatedAt();

        $this->assertSame($data['createdAt'], $createdAt);
        $this->assertSame($data['updatedAt'], $updatedAt);
    }

    /**
     * Equality behavior for Entry::equals() across multiple scenarios.
     *
     * Scenario:
     * - Build a baseline Entry from a deterministic payload (EntryTestData::getOne()).
     * - Clone payload and apply field overrides from the data provider.
     * - Construct the second Entry and compare via equals().
     * - Expectation is driven by the provider: identical → true, any single-field difference → false.
     *
     * Cases covered:
     * - identical payloads (true)
     * - different id (false)
     * - different title (false)
     * - different body (false)
     * - different date (false)
     * - different createdAt (false)
     * - different updatedAt (false)
     *
     * @param array<string,string> $overrides Field overrides applied to the right-hand Entry payload.
     * @param bool $expected Expected equality result.
     * @return void
     *
     * @covers \Daylog\Domain\Models\Entries\Entry::equals
     * @covers \Daylog\Domain\Models\Entries\Entry::fromArray
     * @dataProvider provideEqualityCases
     */
    public function testEqualsWithProvider(array $overrides, bool $expected): void
    {
        // Arrange: baseline payload and entries
        $base = EntryTestData::getOne();

        /** @var Entry $left */
        $left = Entry::fromArray($base);

        $rightData = array_merge($base, $overrides);

        /** @var Entry $right */
        $right = Entry::fromArray($rightData);

        // Act
        $result = $left->equals($right);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider for equals() behavior.
     *
     * Provides pairs of (overrides, expected):
     * - []                              → true  (identical objects)
     * - ['id' => ...]                   → false
     * - ['title' => ...]                → false
     * - ['body' => ...]                 → false
     * - ['date' => ...]                 → false
     * - ['createdAt' => ...]            → false
     * - ['updatedAt' => ...]            → false
     *
     * @return array<string, array{0: array<string,string>, 1: bool}>
     */
    public function provideEqualityCases(): array
    {
        $cases = [];

        $identicalOverrides = [];
        $cases['identical'] = [$identicalOverrides, true];

        $differentId = [];
        $differentId['id'] = '00000000-0000-4000-8000-000000000000';
        $cases['different id'] = [$differentId, false];

        $differentTitle = [];
        $differentTitle['title'] = 'Another title';
        $cases['different title'] = [$differentTitle, false];

        $differentBody = [];
        $differentBody['body'] = 'Another body';
        $cases['different body'] = [$differentBody, false];

        $differentDate = [];
        $differentDate['date'] = '2025-08-14';
        $cases['different date'] = [$differentDate, false];

        $differentCreatedAt = [];
        $differentCreatedAt['createdAt'] = '2025-08-13T10:00:00Z';
        $cases['different createdAt'] = [$differentCreatedAt, false];

        $differentUpdatedAt = [];
        $differentUpdatedAt['updatedAt'] = '2025-08-13T11:00:00Z';
        $cases['different updatedAt'] = [$differentUpdatedAt, false];

        return $cases;
    }
}
