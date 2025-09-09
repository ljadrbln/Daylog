<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;

/**
 * UC-5 / AC-8 — No fields to update.
 *
 * Purpose:
 * Given only an id without any fields to update, the use case must fail
 * validation with NO_FIELDS_TO_UPDATE and must not touch the repository.
 *
 * Mechanics:
 * - Build UpdateEntryRequest with a valid UUID and no updatable fields.
 * - Domain validator throws DomainValidationException('NO_FIELDS_TO_UPDATE').
 * - Assert that repository save was not called (saveCalls() === 0).
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC8_NoFieldsToUpdateTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that absence of updatable fields triggers NO_FIELDS_TO_UPDATE and repo remains untouched.
     *
     * @return void
     */
    public function testNoFieldsToUpdateFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $id = UuidGenerator::generate();

        $payload = [
            'id' => $id, // no title/body/date provided
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $repo      = $this->makeRepo();
        $errorCode = 'NO_FIELDS_TO_UPDATE';
        $validator = $this->makeValidatorThrows($errorCode);

        $this->expectException(DomainValidationException::class);

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
