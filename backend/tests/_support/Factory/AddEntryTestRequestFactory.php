<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Models\Entries\EntryConstraints;

/**
 * Factory for building AddEntry test requests.
 *
 * Purpose:
 * Provide focused builders for UC-1 scenarios (AC1–AC8).
 *
 * Mechanics:
 * - Starts from EntryTestData::getOne() baseline.
 * - Overrides a single field per scenario when needed.
 */
final class AddEntryTestRequestFactory
{
    /**
     * Build a happy-path request using baseline fixture.
     *
     * @return AddEntryRequestInterface
     */
    public static function happy(): AddEntryRequestInterface
    {
        $data = EntryTestData::getOne();

        $request = AddEntryRequest::fromArray($data);

        return $request;
    }

    /**
     * Title becomes an empty string.
     *
     * @return AddEntryRequestInterface
     */
    public static function emptyTitle(): AddEntryRequestInterface
    {
        $data    = EntryTestData::getOne(title: '');
        $request = AddEntryRequest::fromArray($data);

        return $request;
    }

    /**
     * Title exceeds max length (use provider/fixture to craft long string if needed).
     *
     * @return AddEntryRequestInterface
     */
    public static function titleTooLong(): AddEntryRequestInterface
    {
        $title = str_repeat('X', EntryConstraints::TITLE_MAX + 1);
        $data  = EntryTestData::getOne(title: $title);

        $request = AddEntryRequest::fromArray($data);

        return $request;
    }

    /**
     * Body becomes an empty string.
     *
     * @return AddEntryRequestInterface
     */
    public static function emptyBody(): AddEntryRequestInterface
    {
        $data    = EntryTestData::getOne(body: '');
        $request = AddEntryRequest::fromArray($data);

        return $request;
    }

    /**
     * Body exceeds max length.
     *
     * @return AddEntryRequestInterface
     */
    public static function bodyTooLong(): AddEntryRequestInterface
    {
        $body    = str_repeat('Y', EntryConstraints::BODY_MAX + 1);
        $data    = EntryTestData::getOne(body: $body);
        $request = AddEntryRequest::fromArray($data);

        return $request;
    }

    /**
     * Missing date: provide empty string to trigger DATE_REQUIRED.
     *
     * @return AddEntryRequestInterface
     */
    public static function missingDate(): AddEntryRequestInterface
    {
        $data    = EntryTestData::getOne(date: '');
        $request = AddEntryRequest::fromArray($data);

        return $request;
    }

    /**
     * Invalid date format (e.g., 2025/09/12).
     *
     * @return AddEntryRequestInterface
     */
    public static function invalidDateFormat(): AddEntryRequestInterface
    {
        $data    = EntryTestData::getOne(date: '2025/09/12');
        $request = AddEntryRequest::fromArray($data);

        return $request;
    }

    /**
     * Invalid calendar date (e.g., 2025-02-30).
     *
     * @return AddEntryRequestInterface
     */
    public static function invalidCalendarDate(): AddEntryRequestInterface
    {
        $data    = EntryTestData::getOne(date: '2025-02-30');
        $request = AddEntryRequest::fromArray($data);

        return $request;
    }
}
