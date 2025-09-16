<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;
use Daylog\Tests\Support\Assertion\UpdateEntryBodyOnlyAssertions;

/**
 * UC-5 / AC-02 — Happy path (body-only) — Unit.
 *
 * Purpose:
 * Ensure that when only the body is provided with a valid id, the use case
 * updates the body, preserves other fields, refreshes updatedAt per BR-2,
 * and returns a response DTO holding a valid domain Entry snapshot.
 *
 * Mechanics:
 * - Build deterministic rows via UpdateEntryScenario::ac02BodyOnly();
 * - Seed a Fake repository through EntriesSeeding::intoFakeRepo();
 * - Build a body-only request via UpdateEntryTestRequestFactory;
 * - Validator is expected to run exactly once (domain rules tested elsewhere).
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
        $dataset = UpdateEntryScenario::ac02BodyOnly();
        
        $rows    = $dataset['rows'];
        $id      = $dataset['targetId'];
        $newBody = $dataset['newBody'];

        $repo = $this->makeRepo();
        EntriesSeeding::intoFakeRepo($repo, $rows);

        $request   = UpdateEntryTestRequestFactory::bodyOnly($id, $newBody);
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $response = $useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $expected = Entry::fromArray($rows[0]);
        $this->assertBodyOnlyUpdated($expected, $actual, $newBody);
    }
}
