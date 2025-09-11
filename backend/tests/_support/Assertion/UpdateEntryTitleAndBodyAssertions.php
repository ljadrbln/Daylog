<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Assertion;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\DateService;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Assertions for UC-5 partial update (title+body).
 *
 * Purpose:
 * Ensure only 'title' and 'body' changed; 'date' and 'createdAt' intact;
 * timestamps are ISO-8601 UTC and monotonic (updatedAt >= createdAt).
 */
trait UpdateEntryTitleAndBodyAssertions
{
    /**
     * Assert id valid & same; title/body changed; date preserved; timestamps valid/monotonic.
     *
     * @param Entry  $expected
     * @param Entry  $actual
     * @param string $expectedTitle
     * @param string $expectedBody
     * @return void
     */
    protected function assertTitleAndBodyUpdated(Entry $expected, Entry $actual, string $expectedTitle, string $expectedBody): void
    {
        $actualId   = $actual->getId();
        $isValidId = UuidGenerator::isValid($actualId);
        $this->assertTrue($isValidId);

        $expectedId = $expected->getId();
        $this->assertSame($expectedId, $actualId);

        $actualTitle = $actual->getTitle();
        $this->assertSame($expectedTitle, $actualTitle);

        $actualBody = $actual->getBody();
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

        $createdTs = strtotime($createdAt);
        $updatedTs = strtotime($updatedAt);
        $this->assertGreaterThanOrEqual($createdTs, $updatedTs);
    }
}
