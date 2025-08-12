<?php
declare(strict_types=1);

namespace Tests\Unit\Application\UseCases;

use Codeception\Test\Unit;

// These classes do not exist yet — expected Red on first run.
use Daylog\Application\UseCases\Entries\AddEntry;
use Daylog\Application\DTO\Entries\AddEntryRequest;
use Daylog\Domain\Interfaces\EntryRepositoryInterface;
use Daylog\Domain\Models\Entry;
use Daylog\Domain\Errors\ValidationException;

/**
 * UC-1: AddEntry (Use Case)
 *
 * Red tests: happy path + validations.
 * Repository is mocked; DTO is a simple data carrier (no trim/validation inside).
 */
final class AddEntryTest extends Unit
{
    /** @var int Max length for title (domain rule) */
    private const TITLE_MAX = 200;

    /** @var int Max length for body (domain rule) */
    private const BODY_MAX  = 50000;

    /**
     * Ensures AddEntry creates a domain Entry, calls repository->save(Entry)
     * exactly once with correct data, and returns the generated UUID.
     *
     * @return void
     */
    public function testHappyPathSavesEntryAndReturnsUuid(): void
    {
        $title = 'My entry';
        $body  = 'Meaningful body';
        $date  = '2025-08-12';
        $uuid  = '00000000-0000-4000-8000-000000000001';

        $repo = $this->createMock(EntryRepositoryInterface::class);
        $repo
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entry $e) use ($title, $body, $date): bool {
                $sameTitle = $e->getTitle() === $title;
                $sameBody  = $e->getBody()  === $body;
                $sameDate  = $e->getDate()  === $date;
                return $sameTitle && $sameBody && $sameDate;
            }))
            ->willReturn($uuid);

        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $result = $useCase->execute($request);

        $this->assertSame($uuid, $result);
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

        $repo = $this->createMock(EntryRepositoryInterface::class);
        $repo->expects($this->never())->method('save');

        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $this->expectException(ValidationException::class);
        $useCase->execute($request);
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

        $repo = $this->createMock(EntryRepositoryInterface::class);
        $repo->expects($this->never())->method('save');

        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $this->expectException(ValidationException::class);
        $useCase->execute($request);
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

        $repo = $this->createMock(EntryRepositoryInterface::class);
        $repo->expects($this->never())->method('save');

        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $this->expectException(ValidationException::class);
        $useCase->execute($request);
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

        $repo = $this->createMock(EntryRepositoryInterface::class);
        $repo->expects($this->never())->method('save');

        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $this->expectException(ValidationException::class);
        $useCase->execute($request);
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

        $repo = $this->createMock(EntryRepositoryInterface::class);
        $repo->expects($this->never())->method('save');

        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $this->expectException(ValidationException::class);
        $useCase->execute($request);
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

        $repo = $this->createMock(EntryRepositoryInterface::class);
        $repo->expects($this->never())->method('save');

        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $this->expectException(ValidationException::class);
        $useCase->execute($request);
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

        $repo = $this->createMock(EntryRepositoryInterface::class);
        $repo->expects($this->never())->method('save');

        $request = new AddEntryRequest($title, $body, $date);
        $useCase = new AddEntry($repo);

        $this->expectException(ValidationException::class);
        $useCase->execute($request);
    }
}

