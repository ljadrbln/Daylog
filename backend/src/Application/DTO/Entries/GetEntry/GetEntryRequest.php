<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\GetEntry;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;

/**
 * Immutable DTO for retirieving info about existed entry.
 * Implements GetEntryRequestInterface.
 */
final class GetEntryRequest implements GetEntryRequestInterface
{
    /** @var string */
    private string $id;

    /**
     * Private constructor. Use fromArray().
     *
     * @param string $id
     */
    private function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Factory method to create a request from an associative array.
     *
     * @param array<string,string> $data Input array with keys: id.
     * @return GetEntryRequestInterface
     */
    public static function fromArray(array $data): GetEntryRequestInterface
    {
        $id = $data['id'] ?? '';

        $request = new self($id);
        return $request;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        $result = $this->id;
        return $result;
    }
}
