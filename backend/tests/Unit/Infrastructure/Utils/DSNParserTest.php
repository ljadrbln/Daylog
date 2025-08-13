<?php

namespace Daylog\Tests\Unit\Infrastructure\Utils;

use Codeception\Test\Unit;
use Daylog\Infrastructure\Utils\DSNParser;

/**
 * @covers \Diary\Infrastructure\Utils\DSNParser
 *
 * Unit tests for the DSNParser class that parses connection strings
 * into DSN format, username and password components.
 */
class DSNParserTest extends Unit
{
    /**
     * Tests that a valid DSN URL is parsed correctly into [dsn, user, pass].
     */
    public function testParseReturnsDsnUserPass(): void
    {
        $url = 'mysql://root:secret@localhost:3307/mydb';

        [$dsn, $user, $pass] = DSNParser::parse($url);

        $this->assertSame('mysql:host=localhost;port=3307;dbname=mydb', $dsn);
        $this->assertSame('root', $user);
        $this->assertSame('secret', $pass);
    }

    /**
     * Tests that port defaults to 3306 if not provided in DSN.
     */
    public function testParseUsesDefaultPort(): void
    {
        $url = 'pgsql://user:pw@dbhost/database';

        [$dsn, $user, $pass] = DSNParser::parse($url);

        $this->assertSame('pgsql:host=dbhost;port=3306;dbname=database', $dsn);
    }

    /**
     * Tests that missing user triggers exception.
     */
    public function testParseMissingUserThrowsException(): void
    {
        $url = 'mysql://localhost:3306/mydb';

        $this->expectException(\InvalidArgumentException::class);
        DSNParser::parse($url);
    }

    /**
     * Tests that completely invalid URL triggers exception.
     */
    public function testParseCompletelyInvalidUrlThrowsException(): void
    {
        $url = 'invalid';

        $this->expectException(\InvalidArgumentException::class);
        DSNParser::parse($url);
    }
}
