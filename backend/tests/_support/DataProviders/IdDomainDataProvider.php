<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

/**
 * Centralized invalid UUID cases for validator tests.
 */
trait IdDomainDataProvider
{
    /**
     * Provides malformed UUID cases and their expected error codes.
     *
     * Cases:
     * - empty string
     * - too short token
     * - non-hex character
     * - wrong length (35)
     * - bad hyphenation
     * 
     * @return array<string, array{0:string,1:string}>
     */
    public function provideInvalidUuidCases(): array
    {
        $cases = [
            'empty string'       => ['',                                      'ID_REQUIRED'],
            'too short token'    => ['123',                                   'ID_INVALID'],
            'non-hex character'  => ['123e4567-e89b-12d3-a456-42661417400g',  'ID_INVALID'],
            'wrong length (35)'  => ['123e4567-e89b-12d3-a456-42661417400',   'ID_INVALID'],
            'bad hyphenation'    => ['123e4567e89b-12d3-a456-426614174000',   'ID_INVALID'],
        ];
        return $cases;
    }
}