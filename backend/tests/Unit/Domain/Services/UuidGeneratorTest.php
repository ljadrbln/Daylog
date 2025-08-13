<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Domain\Services;

use Codeception\Test\Unit;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Class UuidGeneratorTest
 *
 * Unit tests for the UuidGenerator service.
 * Covers both UUID generation and validation logic.
 */
final class UuidGeneratorTest extends Unit
{
    /**
     * Test that generate() returns a valid UUID v4 string.
     *
     * The generated UUID should match the RFC 4122 v4 pattern.
     *
     * @return void
     */
    public function testGenerateReturnsValidUuid(): void
    {
        $uuid = UuidGenerator::generate();

        $this->assertIsString($uuid, 'Generated UUID should be a string');
        $this->assertTrue(
            UuidGenerator::isValid($uuid),
            sprintf('Generated UUID "%s" should be valid according to isValid()', $uuid)
        );
    }

    /**
     * Test that isValid() correctly validates a valid UUID v4.
     *
     * @return void
     */
    public function testIsValidReturnsTrueForValidUuid(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $this->assertTrue(
            UuidGenerator::isValid($uuid),
            sprintf('UUID "%s" should be considered valid', $uuid)
        );
    }

    /**
     * Test that isValid() correctly rejects invalid UUID strings.
     *
     * @return void
     */
    public function testIsValidReturnsFalseForInvalidUuid(): void
    {
        $invalidUuids = [
            '',
            'not-a-uuid',
            '12345678-1234-1234-1234-1234567890', // too short
            '123e4567-e89b-12d3-a456-426614174000-extra', // too long
            'zzzzzzzz-zzzz-zzzz-zzzz-zzzzzzzzzzzz', // invalid hex
        ];

        foreach ($invalidUuids as $invalid) {
            $this->assertFalse(
                UuidGenerator::isValid($invalid),
                sprintf('Invalid UUID "%s" should be rejected', $invalid)
            );
        }
    }
}
