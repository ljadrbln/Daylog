<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * AC-6: Missing date → DATE_REQUIRED.
 *
 * Purpose:
 *   Ensure that when no date is provided, the use case fails business validation.
 *
 * Mechanics:
 *   - Build a valid baseline payload.
 *   - Remove 'date' key to simulate missing input at Application level.
 *   - Expect DomainValidationException, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 * 
 * @group UC-AddEntry
 */
final class AC6_MissingDateTest extends BaseAddEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-6 Negative path: missing date fails with DATE_REQUIRED.
     *
     * @return void
     */
    public function testMissingDateFailsWithDateRequired(): void
    {
        // Arrange
        $data = EntryTestData::getOne();
        unset($data['date']);

        $request = AddEntryRequest::fromArray($data);

        // Expectation
        $this->expectDateRequired();

        // Act
        $this->useCase->execute($request);

        // Safety
        $message = 'DomainValidationException was expected for missing date';
        $this->fail($message);
    }
}
