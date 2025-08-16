<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Domain\Models;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entry;
use Daylog\Tests\Support\Helper\EntryHelper;

/**
 * Unit-test for the Entry domain model.
 *
 * Verifies that the model holds pre-validated values and exposes consistent getters.
 * This test does not check business-rule validation (handled by Application validators).
 *
 * Cases:
 * - Construct from valid array and assert getters.
 * - Ensure factory method preserves values as-is (no trimming/validation here).
 *
 * @covers \Daylog\Domain\Models\Entry
 */
final class EntryTest extends Unit
{
    public function testConstructAndGetters(): void
    {
        $data  = EntryHelper::getData(); // returns valid title/body/date
        $entry = Entry::fromArray($data);

        $expectedTitle = $data['title'];
        $expectedBody  = $data['body'];
        $expectedDate  = $data['date'];

        $this->assertSame($expectedTitle, $entry->getTitle());
        $this->assertSame($expectedBody,  $entry->getBody());
        $this->assertSame($expectedDate,  $entry->getDate());
    }
}
