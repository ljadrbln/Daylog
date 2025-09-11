<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Assertion;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\DateService;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Assertions for UC-5 title-only update.
 *
 * Purpose:
 * Reuse the same field/timestamp checks across Unit/Integration.
 */
trait UpdateEntryTitleOnlyAssertions
{
    /**
     * Assert id valid & same, only title changed, timestamps valid and monotonic.
     *
     * @param Entry  $expected
     * @param Entry  $actual
     * @param string $expectedTitle
     * @return void
     */
    protected function assertTitleOnlyUpdated(Entry $expected, Entry $actual, string $expectedTitle): void
    {
        $expectedId = $expected->getId();
        $actualId   = $actual->getId();
        
        $isValidId = UuidGenerator::isValid($actualId);
        $this->assertTrue($isValidId);
        $this->assertSame($expectedId, $actualId);

        $actualTitle = $actual->getTitle();
        $this->assertSame($expectedTitle, $actualTitle);

        $expectedBody = $expected->getBody();
        $actualBody   = $actual->getBody();
        $this->assertSame($expectedBody, $actualBody);

        $expectedDate = $expected->getDate();
        $actualDate   = $actual->getDate();
        $this->assertSame($expectedDate, $actualDate);

        $createdAt = $actual->getCreatedAt();
        $updatedAt = $actual->getUpdatedAt();

        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($createdAt);
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($updatedAt);

        $this->assertTrue($isCreatedAtValid);
        $this->assertTrue($isUpdatedAtValid);

        $createdAt = strtotime($createdAt);
        $updatedAt = strtotime($updatedAt);
        $this->assertGreaterThanOrEqual($createdAt, $updatedAt);
    }
}
