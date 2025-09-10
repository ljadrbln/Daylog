<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Normalization\Entries\UpdateEntry;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Normalization\Entries\UpdateEntry\UpdateEntryInputNormalizer;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit tests for UpdateEntryInputNormalizer.
 *
 * Purpose:
 * Validate that only the provided mutable field overrides the entry Entry snapshot,
 * while immutable fields (id, createdAt) are preserved. updatedAt is asserted only
 * by presence and type (caller controls time).
 *
 * @covers \Daylog\Application\Normalization\Entries\UpdateEntry\UpdateEntryInputNormalizer::normalize
 * @group UC-UpdateEntry
 */
final class UpdateEntryInputNormalizerTest extends Unit
{
    /**
     * @dataProvider mergeCases
     *
     * @param string $field One of 'title'|'body'|'date'.
     * @param string $value New value to apply.
     * @return void
     */
    public function testSelectiveMergePreservesImmutables(string $field, string $value): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $id        = $entry->getId();
        $createdAt = $entry->getCreatedAt();

        $payload = ['id' => $id, $field => $value];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Act
        $normalized = UpdateEntryInputNormalizer::normalize($request, $entry);

        // Assert immutables
        $this->assertSame($id, $normalized['id']);
        $this->assertSame($createdAt, $normalized['createdAt']);

        // Expected
        $expectedTitle = ($field === 'title') ? $value : $data['title'];
        $expectedBody  = ($field === 'body')  ? $value : $data['body'];
        $expectedDate  = ($field === 'date')  ? $value : $data['date'];

        $this->assertSame($expectedTitle, $normalized['title']);
        $this->assertSame($expectedBody,  $normalized['body']);
        $this->assertSame($expectedDate,  $normalized['date']);

        // updatedAt must exist and be string
        $this->assertArrayHasKey('updatedAt', $normalized);
        $this->assertIsString($normalized['updatedAt']);
    }

    /**
     * Data provider with named cases.
     *
     * @return array<string, array{0:string,1:string}>
     */
    public function mergeCases(): array
    {
        return [
            'override title' => ['title', 'Updated title'],
            'override body'  => ['body',  'Updated body'],
            'override date'  => ['date',  '2025-09-30'],
        ];
    }
}
