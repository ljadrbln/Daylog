<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * AC-8: Invalid calendar date → DATE_INVALID.
 *
 * Purpose:
 *   Ensure that a date which matches the YYYY-MM-DD format but is not a real
 *   calendar date (e.g., 2025-02-30) triggers a business validation error.
 *
 * Mechanics:
 *   - Build a valid baseline payload.
 *   - Set date to "2025-02-30" (non-existent calendar date).
 *   - Expect DomainValidationException, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 * 
 * @group UC-AddEntry
 */
final class AC8_InvalidCalendarDateTest extends BaseAddEntryIntegrationTest
{
    /**
     * AC-8 Negative path: invalid calendar date fails with DATE_INVALID.
     *
     * @return void
     */
    public function testInvalidCalendarDateFailsWithDateInvalid(): void
    {
        // Arrange
        $data = EntryTestData::getOne(date: '2025-02-30');

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        // Expectation
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        // Act
        $this->useCase->execute($request);

        // Safety (should not reach)
        $message = 'DomainValidationException was expected for invalid calendar date';
        $this->fail($message);
    }
}
