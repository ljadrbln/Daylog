<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\DTO\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\ListEntriesRequest;

/**
 * Tests for ListEntriesRequest::fromArray() — defaults and null handling.
 *
 * Verifies that:
 * - Missing optional filters become null;
 * - Defaults are applied for page/perPage/sort/direction;
 * - No type coercion or trimming is performed.
 *
 * @covers \Daylog\Application\DTO\Entries\ListEntriesRequest
 */
final class ListEntriesRequestFactoryTest extends Unit
{
    /**
     * Ensures defaults and nulls are set when keys are missing.
     *
     * @return void
     */
    public function testFromArraySetsDefaultsAndNullsWhenKeysMissing(): void
    {
        /** Arrange **/
        /** @var array<string,mixed> $params */
        $params = [];

        /** Act **/
        $request = ListEntriesRequest::fromArray($params);

        /** Assert **/
        $this->assertNull($request->getDateFrom());
        $this->assertNull($request->getDateTo());
        $this->assertNull($request->getDate());
        $this->assertNull($request->getQuery());

        $this->assertSame(1, $request->getPage());
        $this->assertSame(10, $request->getPerPage());
        $this->assertSame('date', $request->getSort());
        $this->assertSame('DESC', $request->getDirection());
    }
}
