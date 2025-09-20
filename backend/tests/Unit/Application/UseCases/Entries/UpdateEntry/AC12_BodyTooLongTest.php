<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-12 — Body too long.
 *
 * Purpose:
 *   Given a body longer than 50000 characters, validation must fail with
 *   BODY_TOO_LONG and the repository must not be touched.
 *
 * Mechanics:
 *   - Generate a valid UUID and build a request with body length = BODY_MAX + 1.
 *   - Configure validator mock to throw DomainValidationException('BODY_TOO_LONG').
 *   - Execute the use case and assert that repository save was not called.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC12_BodyTooLongTest extends BaseUpdateEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Validate that too long body triggers BODY_TOO_LONG and repo remains untouched.
     *
     * @return void
     */
    public function testTooLongBodyFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $dataset   = UpdateEntryDataset::ac06InvalidId();
        $request   = $dataset['request'];

        $errorCode = 'BODY_TOO_LONG';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();

        // Expect
        $this->expectBodyTooLong();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
