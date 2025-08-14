<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\AddEntryRequest;
use Daylog\Application\UseCases\Entries\AddEntry;
use Daylog\Domain\Errors\ValidationException;
use Daylog\Domain\Models\Entry;
use Daylog\Domain\Models\EntryConstraints;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;
use Daylog\Tests\Support\Helper\EntryHelper;

/**
 * UC-1: AddEntry (Use Case)
 *
 * Goals:
 *  - Happy path: entry is saved, UUID is returned, and saved data equals input.
 *  - Validation: for invalid input no repository writes happen.
 */
final class AddEntryTest extends Unit
{
    /**
     * Happy path: repository is called once, saved entry matches input,
     * and the returned value looks like a non-empty UUID string.
     *
     * @return void
     */
    public function testHappyPathSavesEntryAndReturnsUuid(): void
    {
        // Arrange
        $data = EntryHelper::getData(); // ['title' => ..., 'body' => ..., 'date' => ...]
        $repo = new FakeEntryRepository();
        $uc   = new AddEntry($repo);
        $req  = AddEntryRequest::fromArray($data);

        // Act
        $uuid = $uc->execute($req);

        // Assert
        $this->assertSame(1, $repo->saveCalls);

        /** @var Entry|null $saved */
        $saved = $repo->savedEntry;
        $this->assertInstanceOf(Entry::class, $saved);
        $this->assertSame($data['title'], $saved->getTitle());
        $this->assertSame($data['body'],  $saved->getBody());
        $this->assertSame($data['date'],  $saved->getDate());

        $this->assertIsString($uuid);
        $this->assertNotEmpty($uuid);
        // UUIDv4 format is covered in UuidGenerator tests; here we only require non-empty string.
    }

    /**
     * Ensures that invalid input triggers ValidationException
     * and that repository->save() is never called.
     *
     * We use try/catch instead of expectException so that we can assert
     * the repository call count after the failure.
     *
     * @dataProvider invalidInputProvider
     *
     * @param array<string,string> $overrides Fields to override in valid base data
     * @return void
     */
    public function testValidationErrorsDoNotTouchRepository(array $overrides): void
    {
        // Arrange
        $data = EntryHelper::getData();
        $data = array_merge($data, $overrides);

        $repo = new FakeEntryRepository();
        $uc   = new AddEntry($repo);
        $req  = AddEntryRequest::fromArray($data);

        // Act + Assert
        try {
            $uc->execute($req);
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertSame(0, $repo->saveCalls);
            $this->assertNull($repo->savedEntry);
        }
    }

    /**
     * Provides field overrides that make the request invalid.
     *
     * @return array<string,array<string,string>>
     */
    public function invalidInputProvider(): array
    {
        return [
            'empty title' => [[
                'title' => '',
            ]],
            'empty body' => [[
                'body'  => '',
            ]],
            'too long title' => [[
                'title' => str_repeat('T', EntryConstraints::TITLE_MAX + 1),
            ]],
            'too long body' => [[
                'body'  => str_repeat('B', EntryConstraints::BODY_MAX + 1),
            ]],
            'invalid date format' => [[
                'date'  => '12-08-2025',
            ]],
            'invalid calendar date' => [[
                'date'  => '2025-02-30',
            ]],
            'missing date' => [[
                'date'  => '',
            ]],
        ];
    }
}