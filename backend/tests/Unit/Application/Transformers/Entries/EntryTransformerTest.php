<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Transformers\Entries;

use Codeception\Test\Unit;
use Daylog\Application\Transformers\Entries\EntryTransformer;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Class EntryTransformerTest
 *
 * Verifies that EntryTransformer converts a Domain Entry into a plain array
 * suitable for  presentation. The transformer is intentionally
 * stateless and performs a 1:1 field mapping without side effects or mutation.
 *
 * Mechanics under test:
 * - Single item transformation: Entry -> array{id,date,title,body,createdAt,updatedAt}
 * - Preserves exact strings without trimming/formatting.
 * - Maintains key order and presence for all expected fields.
 *
 * @covers \Daylog\Application\Transformers\Entries\EntryTransformer
 */
final class EntryTransformerTest extends Unit
{
    /**
     * A single Entry is transformed into an associative array
     * with the exact six keys response row.
     *
     * @return void
     */
    public function testTransformReturnsExpectedArray(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        // Act
        $actual = EntryTransformer::fromEntry($entry);

        // Assert
        $this->assertSame($data, $actual);
    }
}
