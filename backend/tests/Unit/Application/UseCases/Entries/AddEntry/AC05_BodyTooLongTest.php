<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * UC-1 / AC-05 — Body too long — Unit.
 *
 * Purpose:
 * Ensure BODY_TOO_LONG triggers a domain error and the repository is not touched.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC05_BodyTooLongTest extends BaseAddEntryUnitTest
{
    /**
     * BODY_TOO_LONG must stop execution before persistence.
     *
     * @return void
     */
    public function testBodyTooLongStopsBeforePersistence(): void
    {
        // Arrange
        $data = EntryTestData::getOne();

        /** @var \Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        $repo      = $this->makeRepo();
        $errorCode = 'BODY_TOO_LONG';
        $validator = $this->makeValidatorThrows($errorCode);

        // Expect
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
