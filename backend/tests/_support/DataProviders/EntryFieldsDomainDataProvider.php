<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

use Daylog\Domain\Models\Entries\EntryConstraints;

/**
 * Centralized domain-level cases for Entry string fields.
 *
 * Purpose:
 * Provide reusable datasets for domain validation of Entry fields: 'title', 'body', 'date'.
 * Covers empty values, length limits, and date format/calendar validity for UC-1 (Add) and UC-5 (Update).
 *
 * Mechanics:
 * - Each case returns [overrides, expectedErrorCode] and is intended to be merged
 *   into a baseline payload from EntryTestData::getOne().
 * - Update-specific dataset additionally includes the "no fields provided" scenario.
 *
 * Error codes in this provider (Domain → Application):
 * - TITLE_REQUIRED, TITLE_TOO_LONG
 * - BODY_REQUIRED,  BODY_TOO_LONG
 * - DATE_REQUIRED,  DATE_INVALID
 *
 * Note on transport-level errors:
 * Non-string primitive violations like TITLE_MUST_BE_STRING / BODY_MUST_BE_STRING / DATE_MUST_BE_STRING
 * are transport-level concerns and should be provided by a dedicated transport cases provider.
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
        $cases = $this->getCommonInvalidDomainCases();

        return $cases;
    }

    /**
     * Provides invalid domain-level cases for UpdateEntry (UC-5).
     *
     * Mechanics:
     * - Includes a special "no fields provided" case to trigger NO_FIELDS_TO_UPDATE.
     * - Other cases mirror the common domain validation set; missing fields are omitted.
     *
     * @return array<string,array{0:array<string,string>,1:string,2?:bool}>
     */
    public function provideInvalidDomainUpdateEntryCases(): array
    {
        $noFieldsCaseName = 'no fields provided';
        $noFieldsCaseData = [/* overrides */ [], 'NO_FIELDS_TO_UPDATE', true /* idOnly */];

        $common = $this->getCommonInvalidDomainCases();

        $result = [
            $noFieldsCaseName => $noFieldsCaseData,
            // Spread common domain cases (same structure) after the special one
        ] + $common;

        return $result;
    }

    /**
     * Build a reusable set of common domain-level invalid cases for Entry fields.
     *
     * Scenario:
     * Used by both AddEntry (UC-1) and UpdateEntry (UC-5) to validate:
     * - Empty title/body
     * - Title/body exceeding max lengths
     * - Invalid date formats and non-existent calendar dates
     * - Empty date
     *
     * @return array<string,array{0:array<string,string>,1:string}>
     */
    private function getCommonInvalidDomainCases(): array
    {
        $tooLongTitle = str_repeat('T', EntryConstraints::TITLE_MAX + 1);
        $tooLongBody  = str_repeat('B', EntryConstraints::BODY_MAX + 1);

        $cases = [
            'title is empty'           => [['title' => ''],            'TITLE_REQUIRED'],
            'title is too long'        => [['title' => $tooLongTitle], 'TITLE_TOO_LONG'],
            'body is empty'            => [['body' => ''],             'BODY_REQUIRED'],
            'body is too long'         => [['body' => $tooLongBody],   'BODY_TOO_LONG'],
            'date invalid format'      => [['date' => '15-08-2025'],   'DATE_INVALID'],
            'date invalid calendar'    => [['date' => '2025-02-30'],   'DATE_INVALID'],
            'date is empty'            => [['date' => ''],             'DATE_REQUIRED']
        ];

        return $cases;
    }
}
