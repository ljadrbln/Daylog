<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;

/**
 * Class AddEntryRequest
 *
 * Immutable request DTO for UC-1 Add Entry.
 * Carries raw user input: title, body and logical date (YYYY-MM-DD).
 */

final class AddEntryRequest
{
    /** @var string */
    private string $title;

    /** @var string */
    private string $body;

    /** @var string */
    private string $date;

    /**
     * AddEntryRequest constructor.
     *
     * @param string $title Raw title input.
     * @param string $body  Raw body input.
     * @param string $date  Logical entry date in YYYY-MM-DD format.
     */
    public function __construct(string $title, string $body, string $date)
    {
        $this->title = $title;
        $this->body  = $body;
        $this->date  = $date;
    }

    /**
     * Factory method to create a request from an associative array.
     *
     * @param array<string,string> $data Input array with keys: title, body, date.
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $title = $data['title'] ?? '';
        $body  = $data['body']  ?? '';
        $date  = $data['date']  ?? '';

        $request = new self($title, $body, $date);
        return $request;
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
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }
}
