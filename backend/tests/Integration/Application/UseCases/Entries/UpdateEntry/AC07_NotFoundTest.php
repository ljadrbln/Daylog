<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * AC-7 (not found): Given a valid UUID that doesn’t exist, when updating,
 * then validation fails with ENTRY_NOT_FOUND.
 *
 * Purpose:
 *   Verify that the use case reports ENTRY_NOT_FOUND for a well-formed UUID v4
 *   that is absent in storage. Ensures domain checks pass (ID_REQUIRED/ID_INVALID
 *   are not triggered) and the repository lookup governs the outcome.
 *
 * Mechanics:
 *   - Keep DB clean (no row with the tested id).
 *   - Use a syntactically valid UUID v4 which is guaranteed to be absent.
 *   - Provide a valid updatable field (e.g., title) to reach the repository lookup.
 *   - Execute the real use case (Provider + SqlFactory wiring).
 *   - Assert: DomainValidationException with message ENTRY_NOT_FOUND is thrown.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC07_NotFoundTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-7 Not found: valid, absent UUID yields ENTRY_NOT_FOUND.
     *
     * @return void
     */
    public function testNotFoundFailsWithEntryNotFound(): void
    {
        // Arrange
        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::notFound();

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'ENTRY_NOT_FOUND';
        $this->expectExceptionMessage($message);

        // Act
        $this->useCase->execute($request);
    }
}
