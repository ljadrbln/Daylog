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
     * Get original (raw) title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        $result = $this->title;
        return $result;
    }

    /**
     * Get original (raw) body.
     *
     * @return string
     */
    public function getBody(): string
    {
        $result = $this->body;
        return $result;
    }

    /**
     * Get original (raw) date.
     *
     * @return string
     */
    public function getDate(): string
    {
        $result = $this->date;
        return $result;
    }
}
