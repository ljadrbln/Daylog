<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\AddEntry;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;

/**
 * Immutable DTO for adding a new entry.
 * Implements AddEntryRequestInterface.
 */
final class AddEntryRequest implements AddEntryRequestInterface
{
    /**
     * Private constructor. Use fromArray().
     *
     * @param string $title Entry title.
     * @param string $body  Entry body/content.
     * @param string $date  Logical date (YYYY-MM-DD).
     */
    private function __construct(
        private string $title,
        private string $body,
        private string $date
    ) {}

    /**
     * Factory method to create a request from an associative array.
     *
     * @param array<string,string> $data Input array with keys: title, body, date.
     * @return AddEntryRequestInterface
     */
    public static function fromArray(array $data): AddEntryRequestInterface
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
