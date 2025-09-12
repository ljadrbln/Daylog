<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Services\DateService;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Assertion\UpdateEntryDateOnlyAssertions;


/**
 * UC-5 / AC-03 — Happy path (date-only) for UpdateEntry use case.
 *
 * Purpose:
 * Ensure that when only the date is provided with a valid id, the use case
 * updates the date, preserves other fields, refreshes updatedAt per BR-2,
 * and returns a response DTO holding a valid domain Entry snapshot.
 *
 * Mechanics:
 * - Seeds repository with a valid Entry from EntryTestData::getOne().
 * - Builds UpdateEntryRequest with {id, date} only.
 * - Validator is expected to run exactly once (domain rules tested elsewhere).
 * - Asserts: id validity/preservation, field isolation, ISO timestamps, and
 *   BR-2 monotonicity (updatedAt ≥ createdAt).
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC03_HappyPath_DateOnlyTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryDateOnlyAssertions;

    /**
     * Validate date-only update behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathUpdatesDateOnlyAndReturnsResponseDto(): void
    {
        // Arrange: seed an existing entry
        $seedData = EntryTestData::getOne();
        $expected = Entry::fromArray($seedData);

        $repo = $this->makeRepo();
        $repo->save($expected);

        $id      = $expected->getId();
        $expectedDate = '2005-08-14';

        $payload = [
            'id'   => $id,
            'date' => $expectedDate,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Validator: once, OK
        $validator = $this->makeValidatorOk();

        // Act
        $useCase  = $this->makeUseCase($repo, $validator);
        $response = $useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $this->assertDateOnlyUpdated($expected, $actual, $expectedDate);
    }
}
