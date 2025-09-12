<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * AC-4: Empty body → BODY_REQUIRED.
 *
 * Purpose:
 *   Ensure that an empty (post-trim) body triggers a validation error.
 *
 * Mechanics:
 *   - Build a valid baseline payload.
 *   - Set body to an already-trimmed empty string ('').
 *   - Expect DomainValidationException, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 * 
 * @group UC-AddEntry
 */
final class AC4_EmptyBodyTest extends BaseAddEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-4 Negative path: empty body fails with BODY_REQUIRED.
     *
     * @return void
     */
    public function testEmptyBodyFailsWithBodyRequired(): void
    {
        // Arrange
        $data    = EntryTestData::getOne(body: '');
        $request = AddEntryRequest::fromArray($data);

        // Expectation
        $this->expectBodyRequired();

        // Act
        $this->useCase->execute($request);

        // Safety
        $message = 'DomainValidationException was expected for empty body';
        $this->fail($message);
    }
}
