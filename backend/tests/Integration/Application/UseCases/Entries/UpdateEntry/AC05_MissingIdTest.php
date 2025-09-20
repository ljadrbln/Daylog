<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-5 (missing id): Given no id, when updating, then validation fails with ID_REQUIRED.
 *
 * Purpose:
 *   Ensure that the UpdateEntry flow rejects a request lacking the mandatory 'id'
 *   at the boundary with a transport-level validation error (ID_REQUIRED). The test
 *   uses real wiring (Provider + SqlFactory) and a clean DB prepared by the base class.
 *
 * Mechanics:
 *   - Build a request payload without the 'id' key (e.g., only 'title' provided).
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: a DomainValidationException with message ID_REQUIRED is thrown and no DB mutation happens.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC05_MissingIdTest extends BaseUpdateEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-05 Missing id: validation fails with ID_REQUIRED.
     *
     * @return void
     */
    public function testMissingIdFailsValidationWithIdRequired(): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac05MissingId();
        $request = $dataset['request'];

        // Expect
        $this->expectIdRequired();

        // Act
        $this->useCase->execute($request);
    }
}
