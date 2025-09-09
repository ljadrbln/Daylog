<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;

/**
 * UC-5 / AC-6 — Invalid id.
 *
 * Purpose:
 * Given a non-UUID id, validation fails with ID_INVALID and repository is not touched.
 *
 * Mechanics:
 * - Build UpdateEntryRequest with a clearly invalid id string.
 * - Validator mock is configured to throw DomainValidationException('ID_INVALID').
 * - Assert: repo->getSaveCalls() remains 0 (no persistence attempted).
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC6_InvalidIdTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that a non-UUID id fails with ID_INVALID and repo remains untouched.
     *
     * @return void
     */
    public function testInvalidIdFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $payload = [
            'id'    => 'not-a-uuid',
            'title' => 'Updated title',
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $repo      = $this->makeRepo();
        $validator = $this->makeValidatorThrows('ID_INVALID');

        $this->expectException(DomainValidationException::class);

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
