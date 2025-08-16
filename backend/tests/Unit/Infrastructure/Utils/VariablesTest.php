<?php

namespace Daylog\Tests\Unit\Infrastructure\Utils;

use Codeception\Test\Unit;
use Daylog\Infrastructure\Utils\Variables;

/**
 * @covers \Diary\Infrastructure\Utils\Variables
 *
 * Unit tests for the Variables utility class that retrieves configuration values
 * from environment variables and F3 context.
 */
class VariablesTest extends Unit
{
    /**
     * Tests that getDB() returns test DB URL when DIARY_APP_ENV is 'test'.
     */
    public function testGetDBReturnsTestDatabaseUrl(): void
    {
        // Arrange: emulate production host
        $_SERVER['HTTP_HOST'] = 'daylog.localhost.test';

        putenv('DAYLOG_TEST_DATABASE_URL=sqlite://test.db');

        $value = Variables::getDB();

        $this->assertSame('sqlite://test.db', $value);
    }

    /**
     * Tests that getDB() returns production DB URL when HTTP_HOST does not end with .test.
     */
    public function testGetDBReturnsProductionDatabaseUrl(): void
    {
        // Arrange: emulate production host
        $_SERVER['HTTP_HOST'] = 'daylog.localhost';

        // Setup production DB variable
        putenv('DAYLOG_DEV_DATABASE_URL=mysql://root@localhost/db');

        // Act
        $value = Variables::getDB();

        // Assert
        $this->assertSame('mysql://root@localhost/db', $value);
    }

}
