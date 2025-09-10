<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Normalization\Entries\AddEntry;

use Codeception\Test\Unit;
use Daylog\Application\Normalization\Entries\AddEntry\AddEntryInputNormalizer;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Domain\Services\DateService;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit tests for AddEntryInputNormalizer.
 *
 * Purpose:
 * Verifies that AddEntryInputNormalizer assembles a strict payload
 * with technical attributes (id, createdAt, updatedAt). Business validation is out of scope.
 *
 * Coverage:
 * - Technical fields: UUID v4 format, ISO-8601 UTC timestamps, createdAt === updatedAt.
 * 
 * @group UC-AddEntry
 */
final class AddEntryInputNormalizerTest extends Unit
{
    /**
     * Ensures technical fields are present and consistent (UUID v4, ISO-8601 UTC, createdAt === updatedAt).
     *
     * Purpose:
     * Validates the non-content part of the payload required by UC-1:
     * - 'id' matches UUID v4 pattern,
     * - 'createdAt' and 'updatedAt' are ISO-8601 UTC with '+00:00' suffix,
     * - timestamps are equal on creation.
     *
     * @return void
     * @covers \Daylog\Application\Normalization\Entries\AddEntryInputNormalizer
     * 
     */
    public function testTechnicalFieldsAreGeneratedAndConsistent(): void
    {
        $data    = EntryTestData::getOne();
        $request = AddEntryRequest::fromArray($data);
        $params  = AddEntryInputNormalizer::normalize($request);

        // UUID v4
        $id        = $params['id'];
        $isIdValid = UuidGenerator::isValid($id);
        $this->assertTrue($isIdValid);

        $createdAt = $params['createdAt'];
        $updatedAt = $params['updatedAt'];
        
        // ISO-8601 UTC (+00:00)
        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($createdAt);
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($updatedAt);
        $this->assertTrue($isCreatedAtValid);
        $this->assertTrue($isUpdatedAtValid);

        // Snapshot consistency
        $this->assertSame($createdAt, $updatedAt);

        // Also ensure trimmed content survived in the final payload
        $expectedDate  = trim($data['date']);
        $expectedBody  = trim($data['body']);
        $expectedTitle = trim($data['title']);

        $this->assertSame($expectedDate,   $params['date']);
        $this->assertSame($expectedBody,   $params['body']);
        $this->assertSame($expectedTitle,  $params['title']);
    }
}


<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Normalization\Entries\UpdateEntry;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Normalization\Entries\UpdateEntry\UpdateEntryInputNormalizer;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Application\Services\DateService;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit tests for UpdateEntryInputNormalizer.
 *
 * Purpose:
 * Verify that normalization merges optional fields over the current Entry snapshot,
 * preserves immutable fields (id, createdAt), and respects BR-2 for updatedAt:
 * updatedAt := max(previous.updatedAt, now).
 *
 * Mechanics:
 * - Build a current Entry via EntryTestData::getOne() and Entry::fromArray().
 * - Create UpdateEntryRequest with different subsets of fields (title/body/date).
 * - Call UpdateEntryInputNormalizer::normalize() and assert resulting payload.
 *
 * Covered scenarios:
 * - Merge only provided fields; preserve others and immutables.
 * - updatedAt picks max(prev, now) when prev is in the future.
 * - No provided fields keeps values; only updatedAt follows BR-2.
 *
 * @covers \Daylog\Application\Normalization\Entries\UpdateEntry\UpdateEntryInputNormalizer::normalize
 */
final class UpdateEntryInputNormalizerTest extends Unit
{
    /**
     * Merge only provided title; body/date must remain unchanged.
     * Immutable id/createdAt must be preserved.
     * updatedAt must be a valid ISO UTC and >= previous updatedAt.
     *
     * @return void
     */
    public function testMergeProvidedTitleOnlyAndPreserveOthers(): void
    {
        // Arrange
        $actualData  = EntryTestData::getOne();
        $actualEntry = Entry::fromArray($actualData);

        $payload = [
            'id'    => $seedData['id'],
            'title' => 'Updated title',
            // body/date omitted on purpose
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Act
        $newTitle   = $payload['title'];
        $actualId   = $actualEntry->getId();
        $createdAt  = $actualEntry->getCreatedAt();
        $normalized = UpdateEntryInputNormalizer::normalize($request, $entry);

        // Assert: immutables
        $this->assertSame($actualId, $normalized['id']);
        $this->assertSame($createdAt, $normalized['createdAt']);

        // Assert: merged/provided + preserved
        $this->assertSame($newTitle, $normalized['title']);
        $this->assertSame($body,     $normalized['body']);
        $this->assertSame($date,     $normalized['date']);

        // Assert: BR-2 for updatedAt
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($normalized['updatedAt']);
        $this->assertTrue($isUpdatedAtValid);
        $this->assertGreaterThanOrEqual(strtotime($updatedAt), strtotime($normalized['updatedAt']));
    }
}
