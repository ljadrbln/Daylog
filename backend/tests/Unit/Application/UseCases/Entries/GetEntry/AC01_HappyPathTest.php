<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\GetEntry;

use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\GetEntryScenario;

/**
 * UC-3 / AC-01 — Happy path — Unit.
 *
 * Purpose:
 *   Verify that GetEntry validates the request, loads the entity by id from the repository,
 *   and returns a response DTO that carries the same Entry id and fields as originally seeded.
 *
 * Mechanics:
 *   - Build a deterministic dataset via GetEntryScenario::ac01HappyPath();
 *   - Seed a Fake repository with a single row;
 *   - Build a request DTO for the seeded id via the test factory;
 *   - Run the use case with a validator that succeeds exactly once;
 *   - Assert: the returned DTO Entry matches the expected one.
 *
 * @covers \Daylog\Application\UseCases\Entries\GetEntry\GetEntry::execute
 * @group UC-GetEntry
 */
final class AC01_HappyPathTest extends BaseGetEntryUnitTest
{
    /**
     * AC-01: Retrieving an existing entry returns it unchanged.
     *
     * @return void
     */
    public function testHappyPathReturnsEntryById(): void
    {
        // Arrange
        $dataset  = GetEntryScenario::ac01HappyPath();
        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];
        $expected = $dataset['expected'];

        $repo = $this->makeRepo();
        EntriesSeeding::intoFakeRepo($repo, $rows);

        $request   = GetEntryTestRequestFactory::happy($targetId);
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $response = $useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $areEqual = $expected->equals($actual);
        $this->assertTrue($areEqual);
    }
}
