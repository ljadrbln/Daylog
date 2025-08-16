<?php
declare(strict_types=1);

namespace Daylog\Domain\Models;

/**
 * Domain Model: Entry
 *
 * Holds validated data for a diary entry. In this design the domain model assumes
 * that input validation is performed upstream (DTO factory + Application validators).
 * This class does not throw validation exceptions for user input; it keeps invariants
 * internally and exposes simple getters.
 */
final class Entry
{
    private string $title;
    private string $body;
    private string $date;

    /**
     * Create an Entry with already validated values.
     *
     * The caller (DTO factory + validators) must guarantee:
     * - title/body are trimmed and within BR limits;
     * - date is a valid YYYY-MM-DD.
     *
     * @param string $title Pre-validated, trimmed title
     * @param string $body  Pre-validated, trimmed body
     * @param string $date  Pre-validated date in YYYY-MM-DD
     */
    private function __construct(string $title, string $body, string $date)
    {
        $this->title = $title;
        $this->body  = $body;
        $this->date  = $date;
    }

    /**
     * Factory from array. Assumes values are pre-validated upstream.
     *
     * @param array{title:string, body:string, date:string} $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $title = $data['title'];
        $body  = $data['body'];
        $date  = $data['date'];

        $entry = new self($title, $body, $date);

        return $entry;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        $result = $this->title;
        return $result;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        $result = $this->body;
        return $result;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        $result = $this->date;
        return $result;
    }
}

