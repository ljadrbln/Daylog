<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * AC-7: Invalid date input format → DATE_INVALID.
 *
 * Purpose:
 *   Ensure that a date not matching strict YYYY-MM-DD format
 *   triggers a validation error.
 *
 * Mechanics:
 *   - Build a valid baseline payload.
 *   - Set date to an invalid format (e.g., "2025/08/30").
 *   - Expect DomainValidationException, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 * 
 * @group UC-AddEntry
 */
final class AC7_InvalidDateFormatTest extends BaseAddEntryIntegrationTest
{
    /**
     * AC-7 Negative path: invalid format fails with DATE_INVALID.
     *
     * @return void
     */
    public function testInvalidDateFormatFailsWithDateInvalid(): void
    {
        // Arrange
        $data = EntryTestData::getOne(date: '2025/08/30');

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        // Expectation
        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage('DATE_INVALID');

        // Act
        $this->useCase->execute($request);

        // Safety (should not reach)
        $message = 'DomainValidationException was expected for invalid date format';
        $this->fail($message);
    }
}
