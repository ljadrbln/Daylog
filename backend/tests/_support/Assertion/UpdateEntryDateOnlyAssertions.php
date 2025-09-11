<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Assertion;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\DateService;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Assertions for UC-5 date-only update.
 *
 * Purpose:
 * Reuse the same checks that only 'date' changed; timestamps valid/monotonic.
 */
trait UpdateEntryDateOnlyAssertions
{
    /**
     * Assert id valid & same, only date changed, timestamps valid and monotonic.
     *
     * @param Entry  $expected
     * @param Entry  $actual
     * @param string $expectedDate
     * @return void
     */
    protected function assertDateOnlyUpdated(Entry $expected, Entry $actual, string $expectedDate): void
    {
        $actualId   = $actual->getId();
        $isValidId = UuidGenerator::isValid($actualId);
        $this->assertTrue($isValidId);

        $expectedId = $expected->getId();
        $this->assertSame($expectedId, $actualId);

        $actualTitle   = $actual->getTitle();
        $expectedTitle = $expected->getTitle();
        $this->assertSame($expectedTitle, $actualTitle);

        $actualBody   = $actual->getBody();
        $expectedBody = $expected->getBody();
        $this->assertSame($actualBody, $expectedBody);

        $actualDate = $actual->getDate();
        $this->assertSame($expectedDate, $actualDate);

        $createdAt = $actual->getCreatedAt();
        $updatedAt = $actual->getUpdatedAt();

        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($createdAt);
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($updatedAt);
        $this->assertTrue($isCreatedAtValid);
        $this->assertTrue($isUpdatedAtValid);

        $createdTs = strtotime($createdAt);
        $updatedTs = strtotime($updatedAt);
        $this->assertGreaterThanOrEqual($createdTs, $updatedTs);
    }
}
