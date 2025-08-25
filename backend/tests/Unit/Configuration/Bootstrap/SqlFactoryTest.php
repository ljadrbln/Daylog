<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Configuration\Bootstrap;

use Codeception\Test\Unit;
use Daylog\Configuration\Bootstrap\SqlFactory;
use DB\SQL;

/**
 * Purpose:
 *  Verify that SqlFactory returns a shared DB\SQL instance (caching)
 *  and allows resetting it between scenarios.
 *
 * Mechanics:
 *  - Uses an in-memory SQLite DSN via environment so Variables::getDB()
 *    yields a value that DSNParser::parse() can convert to a PDO triple.
 *
 * Cases:
 *  - get() twice → same instance (reference equality).
 *  - reset() → next get() returns a different instance.
 * 
 * @covers \Daylog\Configuration\Bootstrap\SqlFactory
 */
final class SqlFactoryTest extends Unit
{
    /**
     * Prepare environment before each test.
     *
     * Purpose:
     *  Ensure SqlFactory starts from a clean state so caching from previous
     *  tests does not interfere with current assertions.
     *
     * Mechanics:
     *  - Invokes SqlFactory::reset() once per test.
     *
     * @return void
     */
    protected function _before(): void
    {
        // Ensure a clean slate before each test
        SqlFactory::reset();
    }

    /**
     * Ensure that get() returns a shared DB\SQL instance.
     *
     * Scenario:
     *  - Call SqlFactory::get() twice without reset().
     *
     * Expected:
     *  - Both calls return the same reference (===).
     *  - The returned object is an instance of DB\SQL.
     *
     * @return void
     */
    public function testGetReturnsSharedInstance(): void
    {
        $first  = SqlFactory::get();
        $second = SqlFactory::get();

        $isSql = $first instanceof SQL;
        $same  = $first === $second;

        $this->assertTrue($isSql);
        $this->assertTrue($same);
    }

    /**
     * Ensure that reset() clears the cached instance.
     *
     * Scenario:
     *  - Call SqlFactory::get() to create an instance.
     *  - Call SqlFactory::reset() to clear it.
     *  - Call SqlFactory::get() again.
     *
     * Expected:
     *  - The new instance differs by reference from the old one (!==).
     *
     * @return void
     */
    public function testResetCreatesNewInstanceNextTime(): void
    {
        $a = SqlFactory::get();
        SqlFactory::reset();
        $b = SqlFactory::get();

        $different = $a !== $b;

        $this->assertTrue($different);
    }
}
