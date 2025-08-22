<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Normalization\Entries;

use Codeception\Test\Unit;
use Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer;

/**
 * Unit test for ListEntriesInputNormalizer.
 *
 * Purpose:
 * - Ensures normalization rules are applied according to UC-2 requirements.
 * - Covers defaults, clamping, trimming, and empty-to-null conversions.
 * - No business validation is performed here (dates, query length).
 *
 * @covers \Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer
 */
final class ListEntriesInputNormalizerTest extends Unit
{
    public function testDefaultsAppliedWhenMissing(): void
    {
        $input      = [];
        $normalizer = new ListEntriesInputNormalizer();

        $normalized = $normalizer->normalize($input);

        $this->assertSame(1, $normalized['page']);
        $this->assertSame(20, $normalized['perPage']); // default from UC-2
        $this->assertSame('date', $normalized['sortField']);
        $this->assertSame('DESC', $normalized['sortDir']);
        $this->assertNull($normalized['date']);
        $this->assertNull($normalized['dateFrom']);
        $this->assertNull($normalized['dateTo']);
        $this->assertNull($normalized['query']);
    }

    public function testClampsPerPageAndPage(): void
    {
        $input = ['page' => 0, 'perPage' => 9999];
        $normalizer = new ListEntriesInputNormalizer();

        $normalized = $normalizer->normalize($input);

        $this->assertSame(1, $normalized['page']);      // min bound
        $this->assertSame(100, $normalized['perPage']); // max bound
    }

    public function testTrimsAndNormalizesQuery(): void
    {
        $input = ['query' => '   hello   '];
        $normalizer = new ListEntriesInputNormalizer();

        $normalized = $normalizer->normalize($input);

        $this->assertSame('hello', $normalized['query']);
    }

    public function testEmptyStringsBecomeNull(): void
    {
        $input = ['date' => '', 'dateFrom' => '', 'dateTo' => '', 'query' => '   '];
        $normalizer = new ListEntriesInputNormalizer();

        $normalized = $normalizer->normalize($input);

        $this->assertNull($normalized['date']);
        $this->assertNull($normalized['dateFrom']);
        $this->assertNull($normalized['dateTo']);
        $this->assertNull($normalized['query']);
    }

    public function testInvalidSortFallsBackToDefaults(): void
    {
        $input = ['sort' => 'not_field', 'direction' => 'sideways'];
        $normalizer = new ListEntriesInputNormalizer();

        $normalized = $normalizer->normalize($input);

        $this->assertSame('date', $normalized['sortField']);
        $this->assertSame('DESC', $normalized['sortDir']);
    }
}
