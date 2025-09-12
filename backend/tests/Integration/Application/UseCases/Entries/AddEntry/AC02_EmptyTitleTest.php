<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Factory\AddEntryTestRequestFactory;

/**
 * AC-2: Empty title → TITLE_REQUIRED.
 *
 * Purpose:
 *   Ensure that an empty (after trimming) title triggers DomainValidationException.
 *
 * Mechanics:
 *   Build a valid baseline payload, replace title with whitespace,
 *   set expectation for exception, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 * 
 * @group UC-AddEntry
 */
final class AC2_EmptyTitleTest extends BaseAddEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * Validator throws TITLE_REQUIRED; repo must remain untouched.
     *
     * @return void
     */
    public function testEmptyTitleFailsWithTitleRequired(): void
    {
        // Arrange
        $request = AddEntryTestRequestFactory::emptyTitle();

        // Expect
        $this->expectTitleRequired();

        // Act
        $this->useCase->execute($request);

        // Safety
        $message = 'DomainValidationException was expected for empty title';
        $this->fail($message);
    }
}
