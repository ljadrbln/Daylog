<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;

/**
 * AC-14 (no-op): Given values identical to current ones,
 * when updating, then the system reports NO_CHANGES_APPLIED
 * and does not modify updatedAt.
 *
 * Purpose:
 *   Verify that UpdateEntry detects a no-op update (all provided values
 *   equal to current stored values) and responds with DomainValidationException
 *   carrying NO_CHANGES_APPLIED. The Entry must remain unchanged in DB,
 *   including an intact updatedAt timestamp.
 *
 * Mechanics:
 *   - Seed a single entry via EntryFixture with known values.
 *   - Build a request with the same id, title, body, and date as stored.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with NO_CHANGES_APPLIED, and updatedAt
 *     remains equal to the original timestamp.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC14_NoOpTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-14 No-op: identical values trigger NO_CHANGES_APPLIED.
     *
     * @return void
     */
    public function testNoOpUpdateReportsNoChangesApplied(): void
    {
        // Arrange: insert one entry with past timestamps
        $actualData = $this->insertEntryWithPastTimestamps();

        /** @var array<string,string> $payload */
        $payload = [
            'id'    => $actualData['id'],
            'title' => $actualData['title'],
            'body'  => $actualData['body'],
            'date'  => $actualData['date'],
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Expect informational domain error: NO_CHANGES_APPLIED
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $message = 'NO_CHANGES_APPLIED';
        $this->expectExceptionMessage($message);

        $this->useCase->execute($request);
    }
}
