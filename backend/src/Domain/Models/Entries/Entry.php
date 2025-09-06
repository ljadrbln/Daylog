<?php
declare(strict_types=1);

namespace Daylog\Domain\Models\Entries;

/**
 * Domain Model: Entry
 *
 * Holds validated data for a diary entry including identity and timestamps.
 * Validation is performed upstream (DTO factory + Application validators).
 * Invariants enforced by callers:
 * - On creation, a single snapshot time is used: createdAt == updatedAt.
 * - Timestamps are pre-formatted strings (e.g., ISO-8601 or app-agreed format).
 */
final class Entry
{
    /**
     * Create an Entry with already validated values.
     *
     * Caller guarantees that inputs are pre-validated and trimmed.
     * A single snapshot time is passed via $now and applied to both timestamps.
     * 
     * @param string $id        UUID (generated upstream).
     * @param string $title     Pre-validated title.
     * @param string $body      Pre-validated body.
     * @param string $date      Logical date in YYYY-MM-DD.
     * @param string $createdAt Creation timestamp (ISO-8601).
     * @param string $updatedAt Update timestamp (ISO-8601).
     */
    private function __construct(
        private string $id,
        private string $title,
        private string $body,
        private string $date,
        private string $createdAt,
        private string $updatedAt,
    ) {}

    /**
     * Factory from array. Assumes values are pre-validated upstream.
     *
     * @param array{
     *   id:string,
     *   title:string,
     *   body:string,
     *   date:string,
     *   createdAt:string,
     *   updatedAt:string
     * } $data
     * 
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id    = $data['id'];
        $title = $data['title'];
        $body  = $data['body'];
        $date  = $data['date'];
        $createdAt = $data['createdAt'];
        $updatedAt = $data['updatedAt'];

        $entry = new self($id, $title, $body, $date, $createdAt, $updatedAt);

        return $entry;
    }

   /**
     * Title getter.
     *
     * @return string
     */
    public function getTitle(): string
    {
        $result = $this->title;
        return $result;
    }

    /**
     * Body getter.
     *
     * @return string
     */
    public function getBody(): string
    {
        $result = $this->body;
        return $result;
    }

    /**
     * Logical date getter.
     *
     * @return string
     */
    public function getDate(): string
    {
        $result = $this->date;
        return $result;
    }

    /**
     * Identity getter (UUID).
     *
     * @return string
     */
    public function getId(): string
    {
        $result = $this->id;
        return $result;
    }

    /**
     * Creation timestamp getter.
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        $result = $this->createdAt;
        return $result;
    }

    /**
     * Update timestamp getter.
     *
     * @return string
     */
    public function getUpdatedAt(): string
    {
        $result = $this->updatedAt;
        return $result;
    }
}

