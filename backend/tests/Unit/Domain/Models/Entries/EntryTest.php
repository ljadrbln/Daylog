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
     * Verify Entry::equals() behavior with various overrides.
     *
     * @param array<string,string> $overrides Field overrides for right-hand entry.
     * @param bool $expected Expected equality result.
     * @return void
     *
     * @dataProvider equalityProvider
     * @covers \Daylog\Domain\Models\Entries\Entry::equals
     */
    public function testEqualsWithProvider(array $overrides, bool $expected): void
    {
        $base = EntryTestData::getOne();

        $left  = Entry::fromArray($base);

        $right = array_merge($base, $overrides);
        $right = Entry::fromArray($right);

        $result = $left->equals($right);

        $this->assertSame($expected, $result);
    }

    /**
     * Provides overrides for Entry::equals() cases.
     *
     * Format: [overrides, expected]
     *
     * @return array<string,array{array<string,string>,bool}>
     */
    public static function equalityProvider(): array
    {
        return [
            'identical'          => [[], true],
            'different id'       => [['id' => '00000000-0000-4000-8000-000000000000'], false],
            'different title'    => [['title' => 'Another title'], false],
            'different body'     => [['body' => 'Another body'], false],
            'different date'     => [['date' => '2025-08-14'], false],
            'different createdAt'=> [['createdAt' => '2025-08-13T10:00:00Z'], false],
            'different updatedAt'=> [['updatedAt' => '2025-08-13T11:00:00Z'], false],
        ];
    }
}
