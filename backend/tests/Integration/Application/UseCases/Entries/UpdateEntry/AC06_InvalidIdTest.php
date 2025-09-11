<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;

/**
 * AC-6 (invalid id): Given a non-UUID id, when updating, then validation fails with ID_INVALID.
 *
 * Purpose:
 *   Verify that the Application-layer validator rejects a non-UUID identifier with
 *   DomainValidationException('ID_INVALID') before any repository interaction.
 *   The test uses real wiring (Provider + SqlFactory) and a clean DB prepared by the base class.
 *
 * Mechanics:
 *   - Optionally seed one entry to keep fixture flow uniform (not required for this error path).
 *   - Build a request payload with a clearly invalid id string and a valid updatable field (title).
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with message ID_INVALID is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC06_InvalidIdTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-06 Invalid id: non-UUID id fails validation with ID_INVALID.
     *
     * @return void
     */
    public function testInvalidIdFailsValidationWithIdInvalid(): void
    {
        // Arrange: optional seed to keep setup uniform
        $this->insertEntryWithPastTimestamps();

        // Build a request payload with an invalid id (non-UUID) and a valid field to update
        $invalidId = 'not-a-uuid';
        $newTitle  = 'Updated title';

        /** @var array<string,string> $payload */
        $payload = [
            'id'    => $invalidId,
            'title' => $newTitle,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Expect domain-level validation error: ID_INVALID
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'ID_INVALID';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
