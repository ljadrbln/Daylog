<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * AC-3: Title too long → TITLE_TOO_LONG.
 *
 * Purpose:
 *   Ensure that a title exceeding ENTRY-BR-1 limit (after trimming) triggers a validation error.
 *
 * Mechanics:
 *   - Build a valid baseline payload via EntryTestData::getOne().
 *   - Set title to EntryConstraints::TITLE_MAX+1 chars (post-trim state).
 *   - Expect DomainValidationException, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 * 
 * @group UC-AddEntry
 */
final class AC3_TitleTooLongTest extends BaseAddEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * TITLE_TOO_LONG must stop execution before persistence.
     *
     * @return void
     */
    public function testTitleTooLongFailsWithTitleTooLong(): void
    {
        // Arrange
        $title   = str_repeat('A', EntryConstraints::TITLE_MAX+1);
        $data    = EntryTestData::getOne(title: $title);
        $request = AddEntryRequest::fromArray($data);

        // Expectation
        $this->expectTitleTooLong();

        // Act
        $this->useCase->execute($request);

        // Safety
        $message = 'DomainValidationException was expected for over-limit title';
        $this->fail($message);
    }
}
