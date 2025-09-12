<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * AC-6 (invalid id): Given a non-UUID id, when updating, then validation fails with ID_INVALID.
 *
 * Purpose:
 *   Ensure the Application-layer validator rejects a malformed identifier
 *   by throwing DomainValidationException('ID_INVALID') before any persistence.
 *   Uses real wiring (Provider + SqlFactory) and a clean DB prepared by the base class.
 *
 * Mechanics:
 *   - Build a request via UpdateEntryTestRequestFactory::titleOnly() with a non-UUID id.
 *   - Execute real use case from BaseUpdateEntryIntegrationTest.
 *   - Expect DomainValidationException with message 'ID_INVALID'.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC06_InvalidIdTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-06: Non-UUID id fails validation with ID_INVALID.
     *
     * @return void
     */
    public function testInvalidIdFailsValidationWithIdInvalid(): void
    {
        // Arrange
        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::invalidId();

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'ID_INVALID';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
