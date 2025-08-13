<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases;

use Codeception\Test\Unit;

// Expected missing classes on first Red run.
use Daylog\Application\UseCases\Entries\AddEntry;
use Daylog\Application\DTO\Entries\AddEntryRequest;
use Daylog\Domain\Interfaces\EntryRepositoryInterface;
use Daylog\Domain\Models\Entry;
use Daylog\Domain\Errors\ValidationException;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * UC-1: AddEntry (Use Case)
 *
 * Red tests: happy path + validations.
 * Repository is a simple fake (no PHPUnit callbacks / expects()).
 */
final class AddEntryTest extends Unit
{
    /** @var int Max length for title (domain rule) */
    private const TITLE_MAX = 200;

    /** @var int Max length for body (domain rule) */
    private const BODY_MAX  = 50000;

    /**
     * Ensures AddEntry creates a domain Entry, calls repository->save(Entry)
     * exactly once with correct data, and returns a UUID.
     *
     * @return void
     */
    public function testHappyPathSavesEntryAndReturnsUuid(): void
    {
        $title = 'My entry';
        $body  = 'Meaningful body';
        $date  = '2025-08-12';
        $uuid  = '00000000-0000-4000-8000-000000000001';

        $repo = new FakeEntryRepository();
        $repo->returnUuid = $uuid;

        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $result = $useCase->execute($request);

        $saveCalls = $repo->saveCalls;
        $this->assertSame(1, $saveCalls);

        /** @var Entry|null $saved */
        $saved = $repo->savedEntry;
        $this->assertInstanceOf(Entry::class, $saved);

        $savedTitle = $saved?->getTitle();
        $savedBody  = $saved?->getBody();
        $savedDate  = $saved?->getDate();

        $this->assertSame($title, $savedTitle);
        $this->assertSame($body,  $savedBody);
        $this->assertSame($date,  $savedDate);

        $returnedUuid = $result; // or $result->getData()['entryUuid'] if you choose a response object
        $this->assertSame($uuid, $returnedUuid);
    }

    /**
     * Title must not be empty; repository->save() must not be called.
     *
     * @return void
     */
    public function testEmptyTitleThrowsAndDoesNotTouchRepository(): void
    {
        $title = '';
        $body  = 'Body present';
        $date  = '2025-08-12';

        $repo = new FakeEntryRepository();
        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $expected = ValidationException::class;
        $this->expectException($expected);

        $unused = $useCase->execute($request);

        $saveCalls = $repo->saveCalls;
        $this->assertSame(0, $saveCalls);
    }

    /**
     * Body must not be empty; repository->save() must not be called.
     *
     * @return void
     */
    public function testEmptyBodyThrowsAndDoesNotTouchRepository(): void
    {
        $title = 'Valid title';
        $body  = '';
        $date  = '2025-08-12';

        $repo = new FakeEntryRepository();
        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $expected = ValidationException::class;
        $this->expectException($expected);

        $unused = $useCase->execute($request);

        $saveCalls = $repo->saveCalls;
        $this->assertSame(0, $saveCalls);
    }

    /**
     * Title exceeding TITLE_MAX must fail; repository->save() must not be called.
     *
     * @return void
     */
    public function testTooLongTitleThrowsAndDoesNotTouchRepository(): void
    {
        $title = str_repeat('T', self::TITLE_MAX + 1);
        $body  = 'Body present';
        $date  = '2025-08-12';

        $repo = new FakeEntryRepository();
        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $expected = ValidationException::class;
        $this->expectException($expected);

        $unused = $useCase->execute($request);

        $saveCalls = $repo->saveCalls;
        $this->assertSame(0, $saveCalls);
    }

    /**
     * Body exceeding BODY_MAX must fail; repository->save() must not be called.
     *
     * @return void
     */
    public function testTooLongBodyThrowsAndDoesNotTouchRepository(): void
    {
        $title = 'Valid title';
        $body  = str_repeat('B', self::BODY_MAX + 1);
        $date  = '2025-08-12';

        $repo = new FakeEntryRepository();
        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $expected = ValidationException::class;
        $this->expectException($expected);

        $unused = $useCase->execute($request);

        $saveCalls = $repo->saveCalls;
        $this->assertSame(0, $saveCalls);
    }

    /**
     * Invalid date format must fail; repository->save() must not be called.
     *
     * @return void
     */
    public function testInvalidDateFormatThrowsAndDoesNotTouchRepository(): void
    {
        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '12-08-2025';

        $repo = new FakeEntryRepository();
        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $expected = ValidationException::class;
        $this->expectException($expected);

        $unused = $useCase->execute($request);

        $saveCalls = $repo->saveCalls;
        $this->assertSame(0, $saveCalls);
    }

    /**
     * Invalid calendar date must fail; repository->save() must not be called.
     *
     * @return void
     */
    public function testInvalidCalendarDateThrowsAndDoesNotTouchRepository(): void
    {
        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '2025-02-30';

        $repo = new FakeEntryRepository();
        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $expected = ValidationException::class;
        $this->expectException($expected);

        $unused = $useCase->execute($request);

        $saveCalls = $repo->saveCalls;
        $this->assertSame(0, $saveCalls);
    }

    /**
     * Missing date must fail; repository->save() must not be called.
     *
     * @return void
     */
    public function testMissingDateThrowsAndDoesNotTouchRepository(): void
    {
        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '';

        $repo = new FakeEntryRepository();
        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $expected = ValidationException::class;
        $this->expectException($expected);

        $unused = $useCase->execute($request);

        $saveCalls = $repo->saveCalls;
        $this->assertSame(0, $saveCalls);
    }
}
