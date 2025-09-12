<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\GetEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;

/**
 * UC-3 / AC-03 — Not found — Unit.
 *
 * Purpose:
 * Ensure that when the requested id does not exist in the repository,
 * the use case fails with ENTRY_NOT_FOUND and does not perform persistence.
 *
 * Mechanics:
 * - Build request with a fresh UUID v4.
 * - Do not seed the repository so findById() returns null.
 * - Validator is expected to run exactly once and succeed (failure is not from validator).
 * - Expect DomainValidationException('ENTRY_NOT_FOUND') from the use case.
 *
 * @covers \Daylog\Application\UseCases\Entries\GetEntry\GetEntry::execute
 * @group UC-GetEntry
 */
final class AC03_NotFoundTest extends BaseGetEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Missing entry must result in ENTRY_NOT_FOUND and no repository writes.
     *
     * @return void
     */
    public function testAbsentIdYieldsEntryNotFound(): void
    {
        // Arrange
        $validator = $this->makeValidatorOk();
        $request   = GetEntryTestRequestFactory::notFound();
        $repo      = $this->makeRepo();

        // Expect
        $this->expectEntryNotFound();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
