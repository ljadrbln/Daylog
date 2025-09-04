<?php
declare(strict_types=1);

namespace Daylog\Application\Exceptions;

use RuntimeException;
use Daylog\Application\Interfaces\InputValidationExceptionInterface;

/**
 * Thrown when input fails transport-level validation:
 * missing required fields or wrong types before business rules are checked.
 */
final class TransportValidationException extends RuntimeException implements InputValidationExceptionInterface
{
    /** @var string */
    private $error;

    /**
     * @param string $error Error code/message.
     */
    public function __construct(string $error)
    {
        parent::__construct($error);
        $this->error = $error;
    }

    /**
     * @return string Error code/message.
     */
    public function getError(): string
    {
        $error = $this->error;

        return $error;
    }

    /** @inheritDoc */
    public function getCategory(): string
    {
        return 'TRANSPORT';
    }    
}
