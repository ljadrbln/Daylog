<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * AC-2: Empty title → TITLE_REQUIRED.
 *
 * Purpose:
 *   Ensure that an empty (after trimming) title triggers DomainValidationException.
 *
 * Mechanics:
 *   Build a valid baseline payload, replace title with whitespace,
 *   set expectation for exception, then execute the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 */
final class AC2_EmptyTitleTest extends BaseAddEntryIntegrationTest
{
    public function testEmptyTitleFailsWithTitleRequired(): void
    {
        // Arrange
        $data = EntryTestData::getOne(title: '');

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        // Expectation
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        // Act
        $this->useCase->execute($request);

        // Safety (should not reach)
        $message = 'DomainValidationException was expected for empty title';
        $this->fail($message);
    }
}
