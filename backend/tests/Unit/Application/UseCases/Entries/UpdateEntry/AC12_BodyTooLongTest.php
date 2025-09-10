<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Domain\Services\UuidGenerator;

/**
 * UC-5 / AC-12 — Body too long.
 *
 * Purpose:
 * Given a body longer than 50000 characters, validation must fail with
 * BODY_TOO_LONG and repository must not be touched.
 *
 * Mechanics:
 * - Build UpdateEntryRequest with valid UUID and body length = BODY_MAX + 1.
 * - Domain validator throws DomainValidationException('BODY_TOO_LONG').
 * - Assert that Fake repository's saveCalls() remains 0.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC12_BodyTooLongTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that too long body fails with BODY_TOO_LONG and repo remains untouched.
     *
     * @return void
     */
    public function testTooLongBodyFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $id = UuidGenerator::generate();

        $longLength = EntryConstraints::BODY_MAX + 1;
        $longBody   = str_repeat('a', $longLength);

        $payload = [
            'id'   => $id,
            'body' => $longBody,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $repo      = $this->makeRepo();
        $errorCode = 'BODY_TOO_LONG';
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
