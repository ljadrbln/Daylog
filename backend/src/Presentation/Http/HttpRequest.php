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
     * Get query string parameters from the current request.
     *
     * Purpose:
     *   Provide typed access to Fat-Free’s `GET` hive, representing query string values.
     *
     * Mechanics:
     *   - Delegates to `Base::instance()->get('GET')`;
     *   - Casts to array<string,string|int|float|null>;
     *   - Keys are parameter names, values are scalars or null if missing.
     *
     * @return array<string,string|int|float|null> Associative array of query parameters.
     */
    public static function get(): array
    {
        /** @var array<string,string|int|float|null> $query */
        $query  = Base::instance()->get('GET');
        $result = $query;

        return $result;
    }

    /**
     * Get form data parameters from the current request.
     *
     * Purpose:
     *   Provide typed access to Fat-Free’s `POST` hive, representing body fields
     *   when submitted as `application/x-www-form-urlencoded` or multipart form data.
     *
     * Mechanics:
     *   - Delegates to `Base::instance()->get('POST')`;
     *   - Casts to array<string,mixed>;
     *   - Keys are parameter names, values may be scalars, arrays, or uploaded file descriptors.
     *
     * @return array<string,mixed> Associative array of POST parameters.
     */
    public static function post(): array
    {
        /** @var array<string,mixed> $post */
        $post   = Base::instance()->get('POST');
        $result = $post;

        return $result;
    }

    /**
     * Get raw request body and decode JSON.
     *
     * Purpose:
     *   Provides transport-level access to HTTP body, with a single responsibility —
     *   JSON parsing into an associative array.
     *
     * Mechanics:
     *   - Reads `BODY` from Fat-Free’s Base instance;
     *   - Attempts to decode JSON;
     *   - Returns empty array if body is empty or invalid.
     *
     * @return array<string,mixed>
     */
    public static function body(): array
    {
        $data = [];

        /** @var string $raw */
        $raw  = Base::instance()->get('BODY');

        if($raw !== '') {            
            $decoded = json_decode($raw, true);
            
            if(is_array($decoded)) {
                $data = $decoded;
            }
        }

        /** @var array<string,mixed> $data */
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
