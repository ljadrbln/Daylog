<?php
declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries;

use Daylog\Application\DTO\Entries\AddEntryRequest;
use Daylog\Domain\Interfaces\EntryRepositoryInterface;
use Daylog\Domain\Models\Entry;
use Daylog\Domain\Errors\ValidationException;

/**
 * Class AddEntry
 *
 * UC-1: Create a new Entry from user input, validate per business rules,
 * persist via repository, and return the new Entry UUID.
 */
final class AddEntry
{
    /** @var EntryRepositoryInterface */
    private EntryRepositoryInterface $repo;

    /**
     * AddEntry constructor.
     *
     * @param EntryRepositoryInterface $repo Repository responsible for persistence.
     */
    public function __construct(EntryRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Execute the use case.
     *
     * @param AddEntryRequest $request Request DTO with title, body, date.
     * @return string UUID of the newly created entry.
     *
     * @throws ValidationException When validation fails (TITLE_*, BODY_*, DATE_*).
     */
    public function execute(AddEntryRequest $request): string
    {
        $title = $request->getTitle();
        $body  = $request->getBody();
        $date  = $request->getDate();

        $this->validate($title, $body, $date);

        $titleTrimmed = trim($title);
        $bodyTrimmed  = trim($body);
        $dateValue    = $date;

        $data = [
            'title' => $titleTrimmed,
            'body'  => $bodyTrimmed,
            'date'  => $dateValue,
        ];

        $entry = Entry::fromArray($data);

        $uuid = $this->repo->save($entry);
        return $uuid;
    }

    /**
     * Validate inputs according to BR-1..BR-3 and BR-6.
     *
     * @param string $title Raw title.
     * @param string $body  Raw body.
     * @param string $date  Raw date (YYYY-MM-DD).
     *
     * @throws ValidationException
     */
    private function validate(string $title, string $body, string $date): void
    {
        $titleTrimmed = trim($title);
        $titleLen     = mb_strlen($titleTrimmed);

        if ($titleLen === 0) {
            $message = 'TITLE_REQUIRED';
            throw new ValidationException($message);
        }

        if ($titleLen > 200) {
            $message = 'TITLE_TOO_LONG';
            throw new ValidationException($message);
        }

        $bodyLen = mb_strlen($body);
        if ($bodyLen === 0) {
            $message = 'BODY_REQUIRED';
            throw new ValidationException($message);
        }

        if ($bodyLen > 50000) {
            $message = 'BODY_TOO_LONG';
            throw new ValidationException($message);
        }

        if ($date === '') {
            $message = 'DATE_REQUIRED';
            throw new ValidationException($message);
        }

        $matchesFormat = (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
        if (!$matchesFormat) {
            $message = 'DATE_INVALID_FORMAT';
            throw new ValidationException($message);
        }

        $year  = (int) substr($date, 0, 4);
        $month = (int) substr($date, 5, 2);
        $day   = (int) substr($date, 8, 2);

        $isValidDate = checkdate($month, $day, $year);
        if (!$isValidDate) {
            $message = 'DATE_INVALID';
            throw new ValidationException($message);
        }
    }
}
