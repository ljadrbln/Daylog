<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-1 / AC-06 — Missing date — Unit.
 *
 * Purpose:
 * Ensure DATE_REQUIRED is reported and repository is untouched.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC06_MissingDateTest extends BaseAddEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * DATE_REQUIRED must stop execution before persistence.
     *
     * @return void
     */
    public function testMissingDateFailsWithDateRequired(): void
    {
        // Arrange
        $data    = EntryTestData::getOne();
        $request = AddEntryRequest::fromArray($data);
        $errorCode = 'DATE_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();

        // Expect
        $this->expectDateRequired();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
