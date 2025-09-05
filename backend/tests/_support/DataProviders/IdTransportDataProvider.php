<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

/**
 * Centralized transport-level cases for 'id' shape/type validation.
 *
 * Purpose:
 * Provide reusable datasets for factories that perform fail-first
 * transport validation of the 'id' field (presence and primitive type).
 *
 * Mechanics:
 * - Each case returns a raw map (as it comes from transport) and the
 *   expected TransportValidationException code.
 *
 * Typical error codes:
 * - ID_REQUIRED   — when 'id' is missing or null.
 * - ID_NOT_STRING — when 'id' exists but is not a string.
 *
 */
trait IdTransportDataProvider
{
    /**
     * Provides invalid transport-level cases for factories using 'id'.
     *
     * Cases:
     * - id missing/null → ID_REQUIRED
     * - id is array     → ID_NOT_STRING
     * - id is int       → ID_NOT_STRING
     *
     * @return array<string, array{0:array<string,mixed>,1:string}>
     */
    public function provideInvalidTransportIdData(): array
    {
        $cases = [
            'id missing'  => [['id' => null],     'ID_REQUIRED'],
            'id is array' => [['id' => ['oops']], 'ID_NOT_STRING'],
            'id is int'   => [['id' => 123],      'ID_NOT_STRING'],
        ];

        return $cases;
    }
}
