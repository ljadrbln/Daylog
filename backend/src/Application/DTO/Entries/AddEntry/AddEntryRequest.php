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
    /** @var string */
    private string $title;

    /** @var string */
    private string $body;

    /** @var string */
    private string $date;

    /**
     * Private constructor. Use fromArray().
     *
     * @param string $title
     * @param string $body
     * @param string $date
     */
    private function __construct(string $title, string $body, string $date)
    {
        $this->title = $title;
        $this->body  = $body;
        $this->date  = $date;
    }

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
