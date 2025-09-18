<?php
declare(strict_types=1);

namespace Daylog\Presentation\Http;

/**
 * ResponseCode
 *
 * Purpose:
 * Central registry of response codes used in Presentation layer.
 * Provides stable identifiers for all success and error cases
 * exposed through HTTP responses.
 */
final class ResponseCode
{
    // --- Success ---
    public const OK = 'OK';

    // --- Transport validation (HTTP 400) ---
    public const TRANSPORT_INVALID_INPUT = 'TRANSPORT_INVALID_INPUT';

    // --- Domain validation (HTTP 422) ---
    public const ID_REQUIRED          = 'ID_REQUIRED';
    public const ID_INVALID           = 'ID_INVALID';
    public const TITLE_REQUIRED       = 'TITLE_REQUIRED';
    public const TITLE_TOO_LONG       = 'TITLE_TOO_LONG';
    public const BODY_REQUIRED        = 'BODY_REQUIRED';
    public const BODY_TOO_LONG        = 'BODY_TOO_LONG';
    public const DATE_REQUIRED        = 'DATE_REQUIRED';
    public const DATE_INVALID         = 'DATE_INVALID';
    public const DATE_RANGE_INVALID   = 'DATE_RANGE_INVALID';
    public const QUERY_TOO_LONG       = 'QUERY_TOO_LONG';
    public const NO_FIELDS_TO_UPDATE  = 'NO_FIELDS_TO_UPDATE';
    public const NO_CHANGES_APPLIED   = 'NO_CHANGES_APPLIED';

    // --- Not found (HTTP 404) ---
    public const ENTRY_NOT_FOUND      = 'ENTRY_NOT_FOUND';

    // --- Infrastructure / unexpected (HTTP 500) ---
    public const UNEXPECTED_ERROR     = 'UNEXPECTED_ERROR';
}
