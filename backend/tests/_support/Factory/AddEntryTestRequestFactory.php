<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Factory for building AddEntry test requests.
 *
 * Purpose:
 * Provide focused builders for UC-1 scenarios (AC1–AC8) with a single source of truth:
 * payload methods return transport-ready arrays for HTTP/functional tests, while DTO
 * methods wrap those payloads via AddEntryRequest::fromArray() for unit/integration tests.
 *
 * Usage:
 * - Functional (HTTP): use *Payload() to send JSON (allows field omission tweaks if needed).
 * - Unit/Integration: use DTO builders (happy(), emptyTitle(), etc.) that delegate to payloads.
 */
final class AddEntryTestRequestFactory
{
    /**
     * Build the canonical happy-path payload.
     *
     * Mechanics:
     * - Uses EntryTestData::getOne() to ensure consistent defaults.
     *
     * @return array{title:string,body:string,date:string}
     */
    public static function happyPayload(): array
    {
        $data  = EntryTestData::getOne();
        
        $title = $data['title'];
        $body  = $data['body'];
        $date  = $data['date'];

        $payload = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        return $payload;
    }

    /**
     * Build a happy-path DTO using the canonical payload.
     *
     * @return AddEntryRequestInterface
     */
    public static function happy(): AddEntryRequestInterface
    {
        $payload = self::happyPayload();
        $request = AddEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Payload with an empty title (after trimming).
     *
     * @return array{title:string,body:string,date:string}
     */
    public static function emptyTitlePayload(): array
    {
        $data  = EntryTestData::getOne(title: '');
        
        $title = $data['title'];
        $body  = $data['body'];
        $date  = $data['date'];

        $payload = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        return $payload;
    }

    /**
     * DTO with an empty title (after trimming).
     *
     * @return AddEntryRequestInterface
     */
    public static function emptyTitle(): AddEntryRequestInterface
    {
        $payload = self::emptyTitlePayload();
        $request = AddEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Payload with a title exceeding the maximum length.
     *
     * @return array{title:string,body:string,date:string}
     */
    public static function titleTooLongPayload(): array
    {
        $limit = EntryConstraints::TITLE_MAX + 1;
        $title = str_repeat('T', $limit);
        
        $data  = EntryTestData::getOne(title: $title);
        
        $body  = $data['body'];
        $date  = $data['date'];
        $title = $data['title'];

        $payload = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        return $payload;
    }

    /**
     * DTO with a title exceeding the maximum length.
     *
     * @return AddEntryRequestInterface
     */
    public static function titleTooLong(): AddEntryRequestInterface
    {
        $payload = self::titleTooLongPayload();
        $request = AddEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Payload with an empty body (after trimming).
     *
     * @return array{title:string,body:string,date:string}
     */
    public static function emptyBodyPayload(): array
    {
        $data  = EntryTestData::getOne(body: '');

        $title = $data['title'];
        $body  = $data['body'];
        $date  = $data['date'];

        $payload = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        return $payload;
    }

    /**
     * DTO with an empty body (after trimming).
     *
     * @return AddEntryRequestInterface
     */
    public static function emptyBody(): AddEntryRequestInterface
    {
        $payload = self::emptyBodyPayload();
        $request = AddEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Payload with a body exceeding the maximum length.
     *
     * @return array{title:string,body:string,date:string}
     */
    public static function bodyTooLongPayload(): array
    {
        
        $limit = EntryConstraints::BODY_MAX + 1;
        $body  = str_repeat('B', $limit);
        
        $data  = EntryTestData::getOne(body: $body);
        
        $title = $data['title'];
        $date  = $data['date'];
        $body  = $data['body'];

        $payload = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        return $payload;
    }

    /**
     * DTO with a body exceeding the maximum length.
     *
     * @return AddEntryRequestInterface
     */
    public static function bodyTooLong(): AddEntryRequestInterface
    {
        $payload = self::bodyTooLongPayload();
        $request = AddEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Payload with a missing date (empty string).
     *
     * @return array{title:string,body:string,date:string}
     */
    public static function missingDatePayload(): array
    {
        $data  = EntryTestData::getOne(date: '');
        
        $title = $data['title'];
        $body  = $data['body'];
        $date  = $data['date'];

        $payload = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        return $payload;
    }

    /**
     * DTO with a missing date (empty string).
     *
     * @return AddEntryRequestInterface
     */
    public static function missingDate(): AddEntryRequestInterface
    {
        $payload = self::missingDatePayload();
        $request = AddEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Payload with an invalid date format (e.g., 2025/09/12).
     *
     * @return array{title:string,body:string,date:string}
     */
    public static function invalidDateFormatPayload(): array
    {
        $data  = EntryTestData::getOne(date: '2025/09/12');

        $title = $data['title'];
        $body  = $data['body'];
        $date  = $data['date'];

        $payload = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        return $payload;
    }

    /**
     * DTO with an invalid date format.
     *
     * @return AddEntryRequestInterface
     */
    public static function invalidDateFormat(): AddEntryRequestInterface
    {
        $payload = self::invalidDateFormatPayload();
        $request = AddEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Payload with an invalid calendar date (e.g., 2025-02-30).
     *
     * @return array{title:string,body:string,date:string}
     */
    public static function invalidCalendarDatePayload(): array
    {
        $data  = EntryTestData::getOne(date: '2025-02-30');
        
        $title = $data['title'];
        $body  = $data['body'];
        $date  = $data['date'];

        $payload = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        return $payload;
    }

    /**
     * DTO with an invalid calendar date.
     *
     * @return AddEntryRequestInterface
     */
    public static function invalidCalendarDate(): AddEntryRequestInterface
    {
        $payload = self::invalidCalendarDatePayload();
        $request = AddEntryRequest::fromArray($payload);

        return $request;
    }
}
