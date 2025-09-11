<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Services\DateService;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryBodyOnlyAssertions;

/**
 * UC-5 / AC-02 — Happy path (body-only) for UpdateEntry use case.
 *
 * Purpose:
 * Ensure that when only the body is provided with a valid id, the use case
 * updates the body, preserves other fields, refreshes updatedAt per BR-2,
 * and returns a response DTO holding a valid domain Entry snapshot.
 *
 * Mechanics:
 * - Seeds repository with a valid Entry from EntryTestData::getOne().
 * - Builds UpdateEntryRequest with {id, body} only.
 * - Validator is expected to run exactly once (domain rules tested elsewhere).
 * - Asserts: id validity/preservation, field isolation, ISO timestamps, and
 *   BR-2 monotonicity (updatedAt ≥ createdAt).
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC02_HappyPath_BodyOnlyTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryBodyOnlyAssertions;
    
    /**
     * Validate body-only update behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathUpdatesBodyOnlyAndReturnsResponseDto(): void
    {
        // Arrange
        $data   = EntryTestData::getOne();
        $expected = Entry::fromArray($data);

        $repo = $this->makeRepo();
        $repo->save($expected);

        $id      = $expected->getId();
        $newBody = 'Updated body';

        /** @var \Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::bodyOnly($id, $newBody);

        $validator = $this->makeValidatorOk();

        // Act
        $useCase  = $this->makeUseCase($repo, $validator);
        $response = $useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $this->assertBodyOnlyUpdated($expected, $actual, $newBody);
    }
}
