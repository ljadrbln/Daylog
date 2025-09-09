<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;
use Daylog\Domain\Models\Entries\EntryConstraints;

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
trait EntryFieldsDomainDataProvider
{
    /**
     * Provides invalid domain-level cases with expected error codes for AddEntry (UC-1).
     *
     * @return array<string,array{0:array<string,string>,1:string}>
     */
    public function provideInvalidDomainAddEntryCases(): array
    {
        $tooLongTitle = str_repeat('T', EntryConstraints::TITLE_MAX + 1);
        $tooLongBody  = str_repeat('B', EntryConstraints::BODY_MAX + 1);

        return [
            'title is empty'           => [['title' => ''],             'TITLE_REQUIRED'],
            'title is too long'        => [['title' => $tooLongTitle],  'TITLE_TOO_LONG'],
            'body is empty'            => [['body' => ''],              'BODY_REQUIRED'],
            'body is too long'         => [['body' => $tooLongBody],    'BODY_TOO_LONG'],
            'date invalid format'      => [['date' => '15-08-2025'],    'DATE_INVALID'],
            'date invalid calendar'    => [['date' => '2025-02-30'],    'DATE_INVALID'],
            'date is empty'            => [['date' => ''],              'DATE_REQUIRED'],
        ];
    }

    /**
     * Provides invalid domain-level cases for UpdateEntry (UC-5).
     *
     * Mechanics:
     * - 'idOnly' flag indicates that only 'id' must be present in the DTO to trigger NO_FIELDS_TO_UPDATE.
     * - For other cases we return string overrides; missing fields are not included at all.
     *
     * @return array<string,array{0:array<string,string>,1:string,2?:bool}>
     */
    public function provideInvalidDomainUpdateEntryCases(): array
    {
        $tooLongTitle = str_repeat('T', EntryConstraints::TITLE_MAX + 1);
        $tooLongBody  = str_repeat('B', EntryConstraints::BODY_MAX + 1);

        return [
            'no fields provided'    => [/* overrides */ [],           'NO_FIELDS_TO_UPDATE', true /* idOnly */],
            'title is empty'        => [['title' => ''],              'TITLE_REQUIRED'],
            'title is too long'     => [['title' => $tooLongTitle],   'TITLE_TOO_LONG'],
            'body is empty'         => [['body' => ''],               'BODY_REQUIRED'],
            'body is too long'      => [['body' => $tooLongBody],     'BODY_TOO_LONG'],
            'date invalid format'   => [['date' => '15-08-2025'],     'DATE_INVALID'],
            'date invalid calendar' => [['date' => '2025-02-30'],     'DATE_INVALID'],
            'date is empty string'  => [['date' => ''],               'DATE_INVALID'],
        ];
    } 
}
