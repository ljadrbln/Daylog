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
