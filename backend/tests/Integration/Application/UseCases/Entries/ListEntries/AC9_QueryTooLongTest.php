<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

/**
 * AC-9: Given query longer than 30 chars (after trimming), validation fails with QUERY_TOO_LONG.
 *
 * Purpose: 
 *   - Enforce the 30-char limit after trimming for 'query'; 
 *   - overlong values must raise DomainValidationException with QUERY_TOO_LONG.
 * 
 * Mechanics: 
 *   - Build baseline request; 
 *   - provide >30 trimmed-length inputs (with/without surrounding whitespace); 
 *   - expect exception mentioning QUERY_TOO_LONG.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 * 
 * @group UC-ListEntries
 */
final class AC9_QueryTooLongTest extends BaseListEntriesIntegrationTest
{
    /** 
     * AC-9: Overlong query (>30 after trim) fails with QUERY_TOO_LONG. 
     * 
     * @dataProvider provideTooLongQueryValues 
     * 
     * @param string $rawQuery 
     * 
     * @return void
     */
    public function testQueryLongerThan30FailsWithQueryTooLong(string $rawQuery): void
    {
        // Arrange
        $data = ListEntriesHelper::getData();
        $data['query'] = $rawQuery;

        $request = ListEntriesHelper::buildRequest($data);

        // Expect
        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage('QUERY_TOO_LONG');

        // Act
        $this->useCase->execute($request);

        // Safety
        $message = 'DomainValidationException was expected for an overlong query';
        $this->fail($message);
    }

    /**
     * Provide overlong 'query' inputs that still exceed 30 chars after trimming.
     *
     * Scenarios:
     *  - Exactly 31 ASCII chars.
     *  - 31 chars with spaces around (trim does not reduce length below 31).
     *  - 31 chars with newlines/tabs around (trim still leaves 31).
     *
     * @return array<string, array{string}>
     */
    public function provideTooLongQueryValues(): array
    {
        $thirtyOneA = str_repeat('a', 31);
        $withSpaces = '   ' . str_repeat('b', 31) . '   ';
        $withNlTab  = "\n\t" . str_repeat('c', 31) . "\t\n";

        $cases = [
            '31 ascii chars'                         => [$thirtyOneA],
            '31 chars with surrounding spaces'       => [$withSpaces],
            '31 chars with surrounding nl/tab chars' => [$withNlTab],
        ];

        return $cases;
    }
}
