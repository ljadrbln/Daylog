<?php
declare(strict_types=1);

namespace Daylog\Domain\Models;

use Daylog\Domain\Errors\ValidationException;

/**
 * Domain Model: Entry
 *
 * Fields come from the user: title, body, date (YYYY-MM-DD).
 * Validations:
 *  - title: required (trimmed), max 200 chars
 *  - body:  required (trimmed), max 50000 chars
 *  - date:  required, format YYYY-MM-DD, valid calendar date
 */
class Entry
{
    private const TITLE_MAX = 200;
    private const BODY_MAX  = 50000;

    private string $title;
    private string $body;
    private string $date; // stored as 'YYYY-MM-DD'

    /**
     * @param string $title
     * @param string $body
     * @param string $date YYYY-MM-DD
     *
     * @throws ValidationException
     */
    public function __construct(string $title, string $body, string $date)
    {
        $title = trim($title);
        $body  = trim($body);
        $date  = trim($date);

        $this->assertNotEmpty($title, 'title');
        $this->assertNotEmpty($body, 'body');
        $this->assertMaxLength($title, self::TITLE_MAX, 'title');
        $this->assertMaxLength($body, self::BODY_MAX, 'body');
        $this->assertValidDate($date);

        $this->title = $title;
        $this->body  = $body;
        $this->date  = $date;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string YYYY-MM-DD
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $value
     * @param string $field
     *
     * @throws ValidationException
     */
    private function assertNotEmpty(string $value, string $field): void
    {
        if ($value === '') {
            $message = sprintf('Field "%s" must not be empty.', $field);
            throw new ValidationException($message);
        }
    }

    /**
     * @param string $value
     * @param int    $max
     * @param string $field
     *
     * @throws ValidationException
     */
    private function assertMaxLength(string $value, int $max, string $field): void
    {
        $len = mb_strlen($value, 'UTF-8');

        if ($len > $max) {
            $message = sprintf('Field "%s" exceeds max length of %d.', $field, $max);
            throw new ValidationException($message);
        }
    }

    /**
     * @param string $date
     *
     * @throws ValidationException
     */
    private function assertValidDate(string $date): void
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $message = 'Date must match YYYY-MM-DD format.';
            throw new ValidationException($message);
        }

        [$y, $m, $d] = array_map('intval', explode('-', $date));
        if (!checkdate($m, $d, $y)) {
            $message = 'Date is not a valid calendar date.';
            throw new ValidationException($message);
        }
    }
}

