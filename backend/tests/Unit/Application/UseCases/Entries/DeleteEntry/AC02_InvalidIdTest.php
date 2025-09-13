<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\DeleteEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Factory\DeleteEntryTestRequestFactory;

/**
 * UC-4 / AC-02 — Invalid id — Unit.
 *
 * Purpose:
 * Ensure that a non-UUID id is rejected by validation and the repository is not touched.
 *
 * Mechanics:
 * - Build request with an invalid id ('not-a-uuid') via factory.
 * - Configure validator mock to throw DomainValidationException('ID_INVALID').
 * - Execute the use case and assert repository remains untouched.
 *
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry::execute
 * @group UC-DeleteEntry
 */
final class AC02_InvalidIdTest extends BaseDeleteEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Invalid id must trigger ID_INVALID and avoid repository access.
     *
     * @return void
     */
    public function testInvalidIdTriggersValidationError(): void
    {
        // Arrange
        $errorCode = 'ID_INVALID';
        $validator = $this->makeValidatorThrows($errorCode);
        $request   = DeleteEntryTestRequestFactory::invalidId();
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
