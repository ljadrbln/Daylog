<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\AddEntry;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryResponseInterface;

/**
 * Response DTO for UC-1 Add Entry.
 *
 * Encapsulates the newly created entry state. 
 * Factory method `fromArray()` allows constructing from storage/domain data.
 */
final class AddEntryResponse implements AddEntryResponseInterface
{
    private string $id;
    private string $title;
    private string $body;
    private string $date;
    private string $createdAt;
    private string $updatedAt;

    /**
     * Private constructor. Use fromArray().
     *
     * @param string $id
     * @param string $title
     * @param string $body
     * @param string $date
     * @param string $createdAt
     * @param string $updatedAt
     */
    private function __construct(
        string $id,
        string $title,
        string $body,
        string $date,
        string $createdAt,
        string $updatedAt
    ) {
        $this->id        = $id;
        $this->title     = $title;
        $this->body      = $body;
        $this->date      = $date;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Factory method to create a response from an associative array.
     *
     * @param array<string,string> $data Keys: id, title, body, date, createdAt, updatedAt.
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id        = $data['id']        ?? '';
        $title     = $data['title']     ?? '';
        $body      = $data['body']      ?? '';
        $date      = $data['date']      ?? '';
        $createdAt = $data['created_at'] ?? '';
        $updatedAt = $data['updated_at'] ?? '';

        return new self($id, $title, $body, $date, $createdAt, $updatedAt);
    }

    /**
     * @return string The entry identifier (UUID v4).
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string The entry title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string The entry body.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string The logical entry date (YYYY-MM-DD).
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string The creation timestamp (ISO-8601 UTC).
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return string The update timestamp (ISO-8601 UTC).
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
