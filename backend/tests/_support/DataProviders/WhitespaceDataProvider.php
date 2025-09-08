<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

/**
 * Centralized transport-level cases for whitespace trimming.
 *
 * Purpose:
 * Provide reusable datasets for factories and sanitizers that must
 * normalize string fields by removing leading/trailing whitespace (BR-1).
 *
 * Mechanics:
 * - Each case returns the raw input string and the expected trimmed result.
 *
 * Typical patterns:
 * - Leading/trailing spaces.
 * - Tabs.
 * - Newlines.
 * - Mixed whitespace.
 */
trait WhitespaceDataProvider
{
    /**
     * Provides common whitespace variations.
     *
     * @return array<string,array{0:string,1:string}>
     */
    public static function provideWhitespaceCases(): array
    {
        $cases = [
            'spaces'   => ['  foo  ', 'foo'],
            'tabs'     => ["\tbar\t", 'bar'],
            'newlines' => ["\n\nbaz\n", 'baz'],
            'mixed'    => [" \tqux\n ", 'qux'],
        ];

        return $cases;
    }
}
