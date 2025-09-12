<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-08 — No fields to update.
 *
 * Purpose:
 *   Ensure that providing only an id without any updatable fields (title/body/date)
 *   fails early with DomainValidationException('NO_FIELDS_TO_UPDATE') before touching storage.
 *
 * Mechanics:
 *   - Optionally seed one row to keep the integration flow uniform (not required for this error path).
 *   - Generate a valid UUID v4 that may or may not exist — irrelevant for this validation branch.
 *   - Build a request via the shared test factory with only the id (no title/body/date).
 *   - Execute the real use case from the base integration class.
 *   - Expect DomainValidationException with the message 'NO_FIELDS_TO_UPDATE'.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC08_NoFieldsTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-08: Only id provided → NO_FIELDS_TO_UPDATE.
     *
     * @return void
     */
    public function testNoFieldsToUpdateFailsValidationWithNoFieldsToUpdate(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        $id = UuidGenerator::generate();

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::idOnly($id);

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'NO_FIELDS_TO_UPDATE';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
