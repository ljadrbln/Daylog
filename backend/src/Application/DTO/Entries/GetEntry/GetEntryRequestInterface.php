<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\GetEntry;

/**
 * Interface GetEntryRequestInterface
 *
 * Data Transfer Object (DTO) contract for receiving diary entry.
 * 
 * This interface defines the required getters for transporting user input
 * into the application layer when receiving information about existed entry. It ensures a consistent
 * contract between request factories (Presentation layer) and use cases
 * (Application layer).
 */
interface GetEntryRequestInterface
{
    /**
     * Get the id of the entry.
     *
     * @return string Non-empty id string provided by the user.
     */
    public function getId(): string;
}
