<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;

/**
 * AC-8 (no fields): Given only id without any fields to update,
 * when updating, then validation fails with NO_FIELDS_TO_UPDATE.
 *
 * Purpose:
 *   Ensure the Application-layer validator rejects requests that provide
 *   no updatable fields (title/body/date). The use case must fail early
 *   with DomainValidationException('NO_FIELDS_TO_UPDATE') before touching storage.
 *
 * Mechanics:
 *   - Optionally seed a row to keep fixture flow uniform (not required for this error).
 *   - Build a payload that contains only a valid UUID v4 in 'id' and no other fields.
 *   - Execute the real use case (Provider + SqlFactory wiring).
 *   - Assert: DomainValidationException with message NO_FIELDS_TO_UPDATE is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC08_NoFieldsTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-8 No fields: only id provided → NO_FIELDS_TO_UPDATE.
     *
     * @return void
     */
    public function testNoFieldsToUpdateFailsValidationWithNoFieldsToUpdate(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        // Valid UUID v4; request will omit title/body/date on purpose
        $id = UuidGenerator::generate();

        /** @var array<string,string> $payload */
        $payload = [
            'id' => $id,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Expect domain-level validation error: NO_FIELDS_TO_UPDATE
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'NO_FIELDS_TO_UPDATE';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
