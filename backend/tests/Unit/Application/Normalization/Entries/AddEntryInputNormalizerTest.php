<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Normalization\Entries;

use Codeception\Test\Unit;
use Daylog\Application\Normalization\Entries\AddEntryInputNormalizer;
use Daylog\Domain\Services\DateService;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Unit tests for AddEntryInputNormalizer.
 *
 * Purpose:
 * Verifies that AddEntryInputNormalizer trims content fields and assembles a strict payload
 * with technical attributes (id, createdAt, updatedAt). Business validation is out of scope.
 *
 * Coverage:
 * - Field-level trimming for title/body/date (single parametrized test via data provider).
 * - Technical fields: UUID v4 format, ISO-8601 UTC timestamps, createdAt === updatedAt.
 */
final class AddEntryInputNormalizerTest extends Unit
{
    /**
     * Data provider for trimming behavior across all fields (title|body|date).
     *
     * Purpose:
     * Supplies raw input, expected normalized value, and field name under test.
     * Cases cover: both-sides trim, whitespace-only, empty string, tabs/newlines,
     * leading and trailing spaces.
     *
     * @return array<string, array{0:string,1:string,2:string}>
     */
    public function trimProvider(): array
    {
        $cases = [
            // title
            'title trim both sides'      => ['   hello   ', 'hello', 'title'],
            'title whitespace only'      => ['     ', '', 'title'],
            'title empty string'         => ['', '', 'title'],
            'title tabs and newline'     => ["\tfoo\n", 'foo', 'title'],
            'title leading space only'   => [' bar', 'bar', 'title'],
            'title trailing space only'  => ['baz ', 'baz', 'title'],

            // body
            'body trim both sides'       => ['   lorem   ', 'lorem', 'body'],
            'body whitespace only'       => ['     ', '', 'body'],
            'body empty string'          => ['', '', 'body'],
            'body tabs and newline'      => ["\tipsum\n", 'ipsum', 'body'],
            'body leading space only'    => [' dolor', 'dolor', 'body'],
            'body trailing space only'   => ['sit ', 'sit', 'body'],

            // date
            'date trim both sides'       => ['   2025-08-28   ', '2025-08-28', 'date'],
            'date whitespace only'       => ['     ', '', 'date'],
            'date empty string'          => ['', '', 'date'],
            'date tabs and newline'      => ["\t2025-08-28\n", '2025-08-28', 'date'],
            'date leading space only'    => [' 2025-08-28', '2025-08-28', 'date'],
            'date trailing space only'   => ['2025-08-28 ', '2025-08-28', 'date'],
        ];

        return $cases;
    }

    /**
     * Ensures AddEntryInputNormalizer trims fields correctly for title/body/date.
     *
     * Mechanics:
     * - Builds a real AddEntryRequest via fromArray().
     * - Substitutes the tested field with the provided raw input.
     * - Asserts exact normalized value for the targeted field only.
     *
     * @param string $in       Raw input value.
     * @param string $expected Expected normalized value.
     * @param string $field    Field name under test ('title'|'body'|'date').
     * @return void
     *
     * @covers \Daylog\Application\Normalization\Entries\AddEntryInputNormalizer
     * @dataProvider trimProvider
     */
    public function testFieldNormalization(string $in, string $expected, string $field): void
    {
        /**
         * @var array{
         *     title:string,
         *     body:string,
         *     date:string
         * } $data Raw transport map (e.g., $_GET or JSON).
         */
        $data = ['title' => 'T', 'body'  => 'B', 'date'  => '2025-08-28'];
        $data[$field] = $in;

        $normalizer = new AddEntryInputNormalizer(); 
        $normalized = $normalizer->normalize($data);

        $this->assertSame($expected, $normalized[$field]);
    }

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
        $data = [
            'title' => '  Valid title  ',
            'body'  => " Valid body \n",
            'date'  => ' 2025-08-28 ',
        ];

        $normalizer = new AddEntryInputNormalizer();
        $normalized = $normalizer->normalize($data);

        // UUID v4
        $id        = $normalized['id'];
        $isIdValid = UuidGenerator::isValid($id);
        $this->assertTrue($isIdValid);

        $createdAt = $normalized['createdAt'];
        $updatedAt = $normalized['updatedAt'];
        
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

        $this->assertSame($expectedDate,   $normalized['date']);
        $this->assertSame($expectedBody,   $normalized['body']);
        $this->assertSame($expectedTitle,  $normalized['title']);
    }
}
