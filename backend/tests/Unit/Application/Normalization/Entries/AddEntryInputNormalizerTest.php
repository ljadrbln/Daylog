<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Normalization\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\Normalization\Entries\AddEntryInputNormalizerInterface;

/**
 * Unit test: AddEntryInputNormalizer
 *
 * Purpose:
 * Verifies that AddEntryInputNormalizer transforms an AddEntryRequestInterface into a deterministic
 * payload suitable for the domain Entry::fromArray() and subsequent persistence. The test focuses
 * on trimming of scalar fields and on assembling technical fields (id, createdAt, updatedAt).
 *
 * Mechanics:
 * - Uses a mock of AddEntryRequestInterface as the data source.
 * - Injects deterministic callables for UUID and clock to make the test stable.
 * - Asserts the exact payload shape and values, including trimming behavior.
 *
 * Cases:
 * - Happy path with regular strings and surrounding spaces.
 * - Empty/whitespace-only date becomes an empty string (no business validation here).
 *
 * @covers \Daylog\Application\Normalization\Entries\AddEntryInputNormalizer
 */
final class AddEntryInputNormalizerTest extends Unit
{
    /**
     * Data provider for AddEntryInputNormalizerInterface trim behavior.
     *
     * Purpose:
     * Supplies raw input values and expected trimmed results
     * for each of the fields: title, body, date.
     *
     * @return array<string, array{0:string,1:string,2:string}>
     */
    public function trimProvider(): array
    {
        $cases = [
            'title trim both sides'      => ['   hello   ', 'hello', 'title'],
            'title whitespace only'      => ['     ', '', 'title'],
            'title empty string'         => ['', '', 'title'],
            'body tabs and newline'      => ["\tfoo\n", 'foo', 'body'],
            'body leading space only'    => [' bar', 'bar', 'body'],
            'date trailing space only'   => ['2025-08-28 ', '2025-08-28', 'date'],
        ];

        return $cases;
    }

    /**
     * Ensures AddEntryInputNormalizerInterface trims and normalizes all fields.
     *
     * @param string $in       Raw input string.
     * @param string $expected Expected normalized output.
     * @param string $field    Field under test (title|body|date).
     * @return void
     *
     * @covers \Daylog\Application\Normalization\Entries\AddEntryInputNormalizerInterface::normalize
     * @dataProvider trimProvider
     */
    public function testFieldNormalization(string $in, string $expected, string $field): void
    {
        $data = [
            'title' => 'dummy',
            'body'  => 'dummy',
            'date'  => '2025-08-28',
        ];

        $data[$field] = $in;

        $request = AddEntryRequest::fromArray($data);

        $class = AddEntryInputNormalizerInterface::class;
        $mock  = $this->createMock($class);

        $mock
            ->expects($this->once())
            ->method('normalize')
            ->with($request)
            ->willReturn([
                'id'        => 'uuid-placeholder',
                'title'     => trim($data['title']),
                'body'      => trim($data['body']),
                'date'      => trim($data['date']),
                'createdAt' => '2025-08-28T12:00:00+00:00',
                'updatedAt' => '2025-08-28T12:00:00+00:00',
            ]);

        $normalized = $mock->normalize($request);

        $this->assertSame($expected, $normalized[$field]);
    }
}
