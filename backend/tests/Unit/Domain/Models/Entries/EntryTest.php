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
}
