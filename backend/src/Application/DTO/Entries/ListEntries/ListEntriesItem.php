<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\ListEntries;

use Daylog\Domain\Models\Entries\Entry;
/**
 * Class ListEntriesItem
 *
 * Immutable DTO representing a single row in UC‑2 List Entries response.
 * This is a read-model object for presentation; it is not a domain entity.
 *
 * Usage:
 * - Construct via factory {@see ListEntriesItem::fromArray()} using a storage/repository row.
 * - The row is assumed to be already normalized by the Application layer.
 *
 * Expected input format (row):
 * - id:        string UUID v4
 * - date:      string YYYY-MM-DD (logical entry date)
 * - title:     string
 * - body:      string
 * - createdAt: string "YYYY-MM-DD HH:MM:SS" (UTC)
 * - updatedAt: string "YYYY-MM-DD HH:MM:SS" (UTC)
 */
final class ListEntriesItem
{
    /** @var string */
    private string $id;

    /** @var string */
    private string $date;

    /** @var string */
    private string $title;

    /** @var string */
    private string $body;

    /** @var string */
    private string $createdAt;

    /** @var string */
    private string $updatedAt;

    /**
     * Private constructor. Use fromArray().
     *
     * Construct a DTO with already-normalized values.
     *
     * Intended usage: repository/storage returns associative arrays; the use-case
     * maps each row to this DTO via factory methods below without additional mutation.
     *
     * @param string $id        UUID v4.
     * @param string $date      Logical date (YYYY-MM-DD).
     * @param string $title     Entry title.
     * @param string $body      Entry body.
     * @param string $createdAt UTC timestamp "YYYY-MM-DD HH:MM:SS".
     * @param string $updatedAt UTC timestamp "YYYY-MM-DD HH:MM:SS".
     * @return void
     */
    private function __construct(
        string $id,
        string $date,
        string $title,
        string $body,
        string $createdAt,
        string $updatedAt
    ) {
        $this->id        = $id;
        $this->date      = $date;
        $this->title     = $title;
        $this->body      = $body;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Build item from Domain Entry model.
     *
     * @param Entry $entry Domain model carrying validated fields.
     * @return self New instance constructed from the given row.
     */
    public static function fromEntry(Entry $entry): self
    {
        $id        = $entry->getId();
        $title     = $entry->getTitle();
        $body      = $entry->getBody();
        $date      = $entry->getDate();
        $createdAt = $entry->getCreatedAt();
        $updatedAt = $entry->getUpdatedAt();

        $listEntryItem = new self(
            $id,
            $date,
            $title,
            $body,
            $createdAt,
            $updatedAt
        );

        return $listEntryItem;
    }    

    /** @return string */
    public function getId(): string
    {
        $result = $this->id;
        return $result;
    }

    /** @return string */
    public function getDate(): string
    {
        $result = $this->date;
        return $result;
    }

    /** @return string */
    public function getTitle(): string
    {
        $result = $this->title;
        return $result;
    }

    /** @return string */
    public function getBody(): string
    {
        $result = $this->body;
        return $result;
    }

    /** @return string */
    public function getCreatedAt(): string
    {
        $result = $this->createdAt;
        return $result;
    }

    /** @return string */
    public function getUpdatedAt(): string
    {
        $result = $this->updatedAt;
        return $result;
    }

    /**
     * Export DTO to a plain array (useful for serialization).
     *
     * @return array{id:string,date:string,title:string,body:string,createdAt:string,updatedAt:string}
     */
    public function toArray(): array
    {
        $result = [
            'id'        => $this->id,
            'date'      => $this->date,
            'title'     => $this->title,
            'body'      => $this->body,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];

        return $result;
    }
}
