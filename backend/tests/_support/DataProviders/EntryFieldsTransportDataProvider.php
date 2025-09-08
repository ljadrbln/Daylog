<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

/**
 * Centralized transport-level cases for Entry string fields.
 *
 * Purpose:
 * Provide reusable datasets for factories that perform fail-first
 * transport validation of Entry fields: 'title', 'body', 'date'.
 * Each case simulates an invalid primitive type (non-string) so that
 * factories can throw the expected TransportValidationException code.
 *
 * Mechanics:
 * - Returns an array of [overrides, expectedErrorCode].
 * - Intended to be merged into a baseline payload from EntryTestData::getOne().
 *
 * Error codes convention (Presentation → Application):
 * - TITLE_MUST_BE_STRING
 * - BODY_MUST_BE_STRING
 * - DATE_MUST_BE_STRING
 */
trait EntryFieldsTransportDataProvider
{
    /**
     * Provides invalid transport-level cases for Entry fields.
     *
     * Cases:
     * - title is array | int  → TITLE_MUST_BE_STRING
     * - body  is array | int  → BODY_MUST_BE_STRING
     * - date  is array | int  → DATE_MUST_BE_STRING
     *
     * @return array<string, array{0:array<string,mixed>,1:string}>
     */
    public static function provideInvalidTransportEntryData(): array
    {
        $cases = [
            'title is array' => [['title' => ['oops']], 'TITLE_MUST_BE_STRING'],
            'title is int'   => [['title' => 123],      'TITLE_MUST_BE_STRING'],
            'body is array'  => [['body'  => ['oops']], 'BODY_MUST_BE_STRING'],
            'body is int'    => [['body'  => 456],      'BODY_MUST_BE_STRING'],
            'date is array'  => [['date'  => ['oops']], 'DATE_MUST_BE_STRING'],
            'date is int'    => [['date'  => 20250101], 'DATE_MUST_BE_STRING'],
        ];

        return $cases;
    }
}
