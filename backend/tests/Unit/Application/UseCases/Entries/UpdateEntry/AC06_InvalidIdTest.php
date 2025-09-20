<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-06 — Invalid id.
 *
 * Purpose:
 *   Given a non-UUID id, validation must fail with ID_INVALID and the repository
 *   must remain untouched (no save attempts).
 *
 * Mechanics:
 *   - Build UpdateEntryRequest via UpdateEntryTestRequestFactory::titleOnly() with malformed id.
 *   - Configure validator mock to throw DomainValidationException('ID_INVALID').
 *   - Execute the use case and verify no persistence by asserting saveCalls() is zero.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC06_InvalidIdTest extends BaseUpdateEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Validate that a non-UUID id triggers ID_INVALID and repo stays untouched.
     *
     * @return void
     */
    public function testInvalidIdFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $dataset   = UpdateEntryDataset::ac06InvalidId();
        $request   = $dataset['request'];

        $errorCode = 'ID_INVALID';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();

        // Expect
        $this->expectIdInvalid();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
