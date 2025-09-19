<?php
declare(strict_types=1);

namespace Daylog\Presentation\Http;

use Base;
/**
 * Thin wrapper around F3 \Base to access request data.
 *
 * Purpose:
 * Provide controllers with simple static accessors:
 * get(), post(), body(), params().
 *
 * Mechanics:
 * - Delegates to \Base::instance()->get('GET'|'POST'|'BODY'|'PARAMS')
 * - BODY tries to decode JSON first, else returns POST array.
 * - PARAMS normalize keys by removing '@'.
 */
final class HttpRequest
{
    /**
     * @return array<string,string|int|float|null>
     */
    public static function get(): array
    {
        /** @var array<string,string|int|float|null> $query */
        $query  = Base::instance()->get('GET');
        $result = $query;

        return $result;
    }

    /**
     * @return array<string,mixed>
     */
    public static function post(): array
    {
        /** @var array<string,mixed> $post */
        $post   = Base::instance()->get('POST');
        $result = $post;

        return $result;
    }

    /**
     * @return array<string,mixed>
     */
    public static function body(): array
    {
        $data = [];
        $raw  = Base::instance()->get('BODY');

        if($raw) {
            $decoded = json_decode($raw, true);
            if(is_array($decoded)) {
                $data = $decoded;
            }
        }

        return $data;
    }

    /**
     * Get route parameters from F3.
     *
     * Example:
     *   Route: GET /entries/@id
     *   Params: [
     *      [0    => '/entries/798637ef-9aec-4ad6-8c71-daeaef927c5b'],
     *      ['id' => '798637ef-9aec-4ad6-8c71-daeaef927c5b']
     *   ]
     *
     * @return array<string,string> Map of route param keys to their string values.
     */
    public static function params(): array
    {
        /** @var array<string,string> $params */
        $params = Base::instance()->get('PARAMS');

        return $params;
    }
}
