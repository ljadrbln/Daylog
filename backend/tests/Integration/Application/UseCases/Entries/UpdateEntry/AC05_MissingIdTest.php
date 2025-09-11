<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Tests\Support\Assertion\UpdateEntryErrorAssertions;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * AC-5 (missing id): Given no id, when updating, then validation fails with ID_REQUIRED.
 *
 * Purpose:
 *   Ensure that the UpdateEntry flow rejects a request lacking the mandatory 'id'
 *   at the boundary with a transport-level validation error (ID_REQUIRED). The test
 *   uses real wiring (Provider + SqlFactory) and a clean DB prepared by the base class.
 *
 * Mechanics:
 *   - Seed a single entry via EntryFixture (not strictly required for this error, but keeps setup consistent).
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
    use UpdateEntryErrorAssertions;

    /**
     * AC-05 Missing id: validation fails with ID_REQUIRED.
     *
     * @return void
     */
    public function testMissingIdFailsValidationWithIdRequired(): void
    {
        // Arrange
        $title = 'Updated title';

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::missingIdWithTitle($title);

        // Expect
        $this->expectIdRequired();

        // Act
        $this->useCase->execute($request);
    }
}
