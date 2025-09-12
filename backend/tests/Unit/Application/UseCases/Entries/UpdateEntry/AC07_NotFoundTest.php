<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-07 — Not found.
 *
 * Purpose:
 *   Given a valid UUID that does not exist in the repository, the use case must
 *   fail with ENTRY_NOT_FOUND and must not perform any persistence.
 *
 * Mechanics:
 *   - Build request with a freshly generated UUID.
 *   - Do not seed the repository so findById() yields null.
 *   - Domain validator is expected to run exactly once and pass (not the source of failure).
 *   - Expect DomainValidationException('ENTRY_NOT_FOUND') from the use case.
 *   - Verify that repository save was never invoked.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC07_NotFoundTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate not-found behavior and that repository remains untouched on failure.
     *
     * @return void
     */
    public function testValidAbsentUuidTriggersEntryNotFound(): void
    {
        // Arrange: request with a valid but absent UUID
        $id      = UuidGenerator::generate();
        $newTitle = 'Updated title';

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::titleOnly($id, $newTitle);

        $repo = $this->makeRepo();

        // Validator passes once (not the source of failure)
        $validator = $this->makeValidatorOk();

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert: repository untouched
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
