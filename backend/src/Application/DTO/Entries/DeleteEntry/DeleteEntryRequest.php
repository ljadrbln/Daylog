<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\DeleteEntry;

/**
 * Concrete request DTO for UC-4 DeleteEntry.
 *
 * Purpose:
 * Carry the entry UUID into the use case layer, after presentation-level checks.
 *
 * Mechanics:
 * - Built via static factory from an associative array with key 'id'.
 * - Stores the UUID string; no domain validation happens here.
 */
final class DeleteEntryRequest implements DeleteEntryRequestInterface
{
    /** @var string */
    private string $id;

    /**
     * Private constructor. Use fromArray().
     * 
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Factory method to create a request from an associative array.
     *
     * @param array<string,string> $data Input array with keys: id.
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? '';

        $request = new self($id);
        return $request;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): string
    {
        $result = $this->id;
        return $result;
    }
}
