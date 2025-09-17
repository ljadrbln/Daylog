<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Domain\Services;

use Codeception\Test\Unit;
use Daylog\Domain\Services\Clock;
use DateTime;
use DateTimeZone;
use DateTimeImmutable;
/**
 * Unit test for infrastructure time source (Clock).
 *
 * Ensures Clock::now() returns current time in UTC, formatted as ISO-8601 (BR-2).
 * Mechanics:
 *  - Parse result with PHP's ISO-8601 format (DateTime::ATOM).
 *  - Verify UTC offset (+00:00) and zero seconds offset.
 *  - Verify the timestamp is close to system UTC time (≤ 2 seconds).
 *
 * @covers \Daylog\Domain\Services\Clock
 */
final class ClockTest extends Unit
{
    public function testNowReturnsUtcIso8601(): void
    {
        /** Act **/
        $result = Clock::now();

        /** Assert: parseable ISO-8601 **/
        $format   = DateTime::ATOM; // Y-m-d\TH:i:sP (ISO-8601)
        $parsed   = DateTimeImmutable::createFromFormat($format, $result);
        $message1 = 'Clock::now() must return a valid ISO-8601 string (DateTime::ATOM).';
        $this->assertNotFalse($parsed, $message1);

        /** Assert: UTC offset (+00:00) */
        $offset   = $parsed !== false 
            ? $parsed->format('P') 
            : '';

        $message2 = 'Clock::now() must have UTC offset (+00:00).';
        $this->assertSame('+00:00', $offset, $message2);

        /** Assert: zero offset in seconds */
        $seconds  = $parsed !== false 
            ? $parsed->getOffset() 
            : PHP_INT_MAX;

        $message3 = 'Clock::now() must have zero UTC offset.';
        $this->assertSame(0, $seconds, $message3);

        /** Assert: close to system "now" (≤ 2 seconds) **/
        $timezone  = new DateTimeZone('UTC');
        $systemNow = new DateTimeImmutable('now', $timezone);
        $delta     = $parsed !== false 
            ? abs($systemNow->getTimestamp() - $parsed->getTimestamp()) 
            : PHP_INT_MAX;
            
        $message4  = 'Clock::now() must be within 2 seconds of system time (UTC).';
        $this->assertLessThanOrEqual(2, $delta, $message4);
    }
}
