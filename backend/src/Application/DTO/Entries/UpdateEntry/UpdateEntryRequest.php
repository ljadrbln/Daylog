<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\UpdateEntry;

/**
 * Immutable DTO for updating an existing entry (UC-5).
 * Implements UpdateEntryRequestInterface.
 *
 * Purpose:
 * - Transport sanitized, type-checked user input into the Application layer.
 * - Distinguish between absent fields (null) and provided fields (string).
 */
final class UpdateEntryRequest implements UpdateEntryRequestInterface
{
    private function __construct(
        private string $id,
        private ?string $title,
        private ?string $body,
        private ?string $date
    ) {}

    /**
     * Factory method to create a request from an associative array.
     *
     * @param array{
     *  id:string, 
     *  title?:string, 
     *  body?:string, 
     *  date?:string} $data Input array with keys: id, title?, body?, date?.
     * @return UpdateEntryRequestInterface
     */
    public static function fromArray(array $data): UpdateEntryRequestInterface
    {
        $id    = $data['id'];
        $title = $data['title'] ?? null;
        $body  = $data['body']  ?? null;
        $date  = $data['date']  ?? null;

        $request = new self($id, $title, $body, $date);
        return $request;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        $result = $this->id;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        $result = $this->title;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): ?string
    {
        $result = $this->body;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getDate(): ?string
    {
        $result = $this->date;
        return $result;
    }
}
