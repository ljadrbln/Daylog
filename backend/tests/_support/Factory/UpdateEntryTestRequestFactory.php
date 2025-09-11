<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;

/**
 * Test factory for UpdateEntry requests.
 *
 * Purpose:
 * Build DTOs consistently for Unit/Integration AC-tests.
 */
final class UpdateEntryTestRequestFactory
{
    /**
     * Build title-only request.
     *
     * @param string $id UUID v4.
     * @param string $title New title.
     * @return UpdateEntryRequestInterface
     */
    public static function titleOnly(string $id, string $title): UpdateEntryRequestInterface
    {
        $payload = [
            'id'    => $id,
            'title' => $title,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build body-only UpdateEntry request.
     *
     * @param string $id   UUID v4 identifier.
     * @param string $body New body text.
     * @return UpdateEntryRequestInterface
     */
    public static function bodyOnly(string $id, string $body): UpdateEntryRequestInterface
    {
        $payload = [
            'id'   => $id,
            'body' => $body,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build date-only UpdateEntry request.
     *
     * @param string $id   UUID v4 identifier.
     * @param string $date Logical date in YYYY-MM-DD.
     * @return UpdateEntryRequestInterface
     */
    public static function dateOnly(string $id, string $date): UpdateEntryRequestInterface
    {
        $payload = [
            'id'   => $id,
            'date' => $date,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build partial UpdateEntry request: title + body (date omitted).
     *
     * @param string $id
     * @param string $title
     * @param string $body
     * @return UpdateEntryRequestInterface
     */
    public static function titleAndBody(string $id, string $title, string $body): UpdateEntryRequestInterface
    {
        $payload = [
            'id'    => $id,
            'title' => $title,
            'body'  => $body,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid request: missing id (empty after trimming).
     *
     * @param string $title
     * @return UpdateEntryRequestInterface
     */
    public static function missingIdWithTitle(string $title): UpdateEntryRequestInterface
    {
        $payload = ['id' => '', 'title' => $title];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid request: id only (no updatable fields).
     *
     * @param string $id
     * @return UpdateEntryRequestInterface
     */
    public static function idOnly(string $id): UpdateEntryRequestInterface
    {
        $payload = ['id' => $id];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);
        
        return $request;
    }    
}
