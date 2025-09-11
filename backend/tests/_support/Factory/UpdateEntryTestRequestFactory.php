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
}
