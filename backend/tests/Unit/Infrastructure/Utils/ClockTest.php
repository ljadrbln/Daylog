<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Utils;

use Codeception\Test\Unit;
use Daylog\Infrastructure\Clock\Clock;

/**
 * Unit test for infrastructure time source (Clock).
 *
 * Ensures Clock::now() returns current time in UTC, formatted as ISO-8601 (BR-4).
 * Mechanics:
 *  - Parse result with PHP's ISO-8601 format (DateTime::ATOM).
 *  - Verify timezone is exactly UTC.
 *  - Verify the timestamp is close to system UTC time (≤ 2 seconds).
 *
 * @covers \Daylog\Infrastructure\Utils\Clock
 */
final class ClockTest extends Unit
{
    public function testNowReturnsUtcIso8601(): void
    {
        /** Act **/
        $result = Clock::now();

        /** Assert: parseable ISO-8601 **/
        $format   = \DateTime::ATOM; // Y-m-d\TH:i:sP (ISO-8601)
        $parsed   = \DateTimeImmutable::createFromFormat($format, $result);
        $message1 = 'Clock::now() must return a valid ISO-8601 string (DateTime::ATOM).';
        $this->assertNotFalse($parsed, $message1);

        /** Assert: exactly UTC timezone **/
        $tz       = $parsed !== false ? $parsed->getTimezone()->getName() : '';
        $message2 = 'Clock::now() must be in UTC timezone.';
        $this->assertSame('UTC', $tz, $message2);

        /** Assert: close to system "now" (≤ 2 seconds) **/
        $systemNow = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $delta     = $parsed !== false ? abs($systemNow->getTimestamp() - $parsed->getTimestamp()) : PHP_INT_MAX;
        $message3  = 'Clock::now() must be within 2 seconds of system time (UTC).';
        $this->assertLessThanOrEqual(2, $delta, $message3);
    }
}
