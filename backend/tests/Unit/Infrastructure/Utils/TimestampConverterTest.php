<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Utils;

use Codeception\Test\Unit;
use Daylog\Infrastructure\Utils\TimestampConverter;

/**
 * Unit tests for TimestampConverter (ISO <-> SQL conversions).
 *
 * Purpose:
 * Verify that the converter provides **lossless round-trip conversion**
 * between domain-level ISO-8601 UTC strings (e.g. "2025-09-10T19:18:42+00:00")
 * and infrastructure-level SQL DATETIME strings (e.g. "2025-09-10 19:18:42").
 *
 * Test mechanics:
 * - Positive: ISO string → SQL DATETIME → back to ISO must return the original.
 * - Negative: Non-UTC ISO inputs must be rejected with exception.
 *
 * @covers \Daylog\Infrastructure\Utils\TimestampConverter::isoToSqlUtc
 * @covers \Daylog\Infrastructure\Utils\TimestampConverter::sqlToIsoUtc
 */
final class TimestampConverterTest extends Unit
{
    /**
     * Happy path: ensure that a valid ISO-8601 UTC string
     * is converted to SQL DATETIME and back without data loss.
     *
     * Steps:
     * 1. Start with a strict ISO-8601 UTC string.
     * 2. Convert ISO → SQL, assert exact SQL format.
     * 3. Convert SQL → ISO, assert exact match with original.
     *
     * Expected:
     * - SQL has format "Y-m-d H:i:s".
     * - Round-trip preserves the instant down to seconds.
     */
    public function testIsoToSqlAndBackRoundTrip(): void
    {
        $iso = '2025-09-10T19:18:42+00:00';

        $sql  = TimestampConverter::isoToSqlUtc($iso);
        $iso2 = TimestampConverter::sqlToIsoUtc($sql);

        $this->assertSame('2025-09-10 19:18:42', $sql);
        $this->assertSame($iso, $iso2);
    }

    /**
     * Negative case: reject non-UTC ISO input.
     *
     * Steps:
     * 1. Provide a valid ISO string but with +03:00 offset.
     * 2. Attempt ISO → SQL conversion.
     *
     * Expected:
     * - Method throws InvalidArgumentException
     *   because only strict "+00:00" offset is allowed.
     */
    public function testIsoToSqlRejectsNonUtc(): void
    {
        $nonUtc = '2025-09-10T22:18:42+03:00';

        $this->expectException(\InvalidArgumentException::class);

        /** @var string $ignored */
        $ignored = TimestampConverter::isoToSqlUtc($nonUtc);
    }
}
