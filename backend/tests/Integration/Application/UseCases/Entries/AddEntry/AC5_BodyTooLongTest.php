<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * AC-5: Body too long → BODY_TOO_LONG.
 *
 * Purpose:
 *   Ensure that a body exceeding BR-2 limit (after trimming) triggers a validation error.
 *
 * Mechanics:
 *   - Build a valid baseline payload via EntryTestData::getOne().
 *   - Set body to 50001 chars (post-trim state).
 *   - Expect DomainValidationException, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 */
final class AC5_BodyTooLongTest extends BaseAddEntryIntegrationTest
{
    /**
     * AC-5 Negative path: over-limit body fails with BODY_TOO_LONG.
     *
     * @return void
     */
    public function testBodyTooLongFailsWithBodyTooLong(): void
    {
        // Arrange
        $data = EntryTestData::getOne();
        $tooLong = str_repeat('B', 50001);
        $data['body'] = $tooLong;

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        // Expectation
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        // Act
        $this->useCase->execute($request);

        // Safety (should not reach)
        $message = 'DomainValidationException was expected for over-limit body';
        $this->fail($message);
    }
}
