<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * UC-1 / AC-04 — Empty body — Unit.
 *
 * Purpose:
 * Ensure BODY_REQUIRED is thrown and repository remains untouched.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC04_EmptyBodyTest extends BaseAddEntryUnitTest
{
    /**
     * BODY_REQUIRED must stop execution before persistence.
     *
     * @return void
     */
    public function testEmptyBodyFailsWithBodyRequired(): void
    {
        // Arrange
        $data = EntryTestData::getOne();

        /** @var \Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        $repo      = $this->makeRepo();
        $errorCode = 'BODY_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);

        // Expect
        $exceptionClass = \Daylog\Application\Exceptions\DomainValidationException::class;
        $this->expectException($exceptionClass);

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
