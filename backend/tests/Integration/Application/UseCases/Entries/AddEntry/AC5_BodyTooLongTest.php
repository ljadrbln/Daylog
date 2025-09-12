<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * AC-5: Body too long → BODY_TOO_LONG.
 *
 * Purpose:
 *   Ensure that a body exceeding ENTRY-BR-2 limit (after trimming) triggers a validation error.
 *
 * Mechanics:
 *   - Build a valid baseline payload via EntryTestData::getOne().
 *   - Set body to EntryConstraints chars (post-trim state).
 *   - Expect DomainValidationException, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 * 
 * @group UC-AddEntry
 */
final class AC5_BodyTooLongTest extends BaseAddEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-5 Negative path: over-limit body fails with BODY_TOO_LONG.
     *
     * @return void
     */
    public function testBodyTooLongFailsWithBodyTooLong(): void
    {
        // Arrange
        $body    = str_repeat('B', EntryConstraints::BODY_MAX+1);
        $data    = EntryTestData::getOne(body: $body);
        $request = AddEntryRequest::fromArray($data);

        // Expect
        $this->expectBodyTooLong();

        // Act
        $this->useCase->execute($request);

        // Safety
        $message = 'DomainValidationException was expected for over-limit body';
        $this->fail($message);
    }
}
