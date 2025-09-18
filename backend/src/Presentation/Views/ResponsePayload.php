<?php
declare(strict_types=1);

namespace Daylog\Presentation\Views;

/**
 * ResponsePayload is a unified container for rendering in View layer.
 *
 * Purpose:
 * - Provide a single response format for both API and Page controllers.
 * - Encapsulate success flag, status code, optional message code, and payload data.
 * - Simplify ViewResponse logic by always exposing toArray().
 *
 * Usage:
 * - Controllers build ResponsePayload via success()/failure() factories.
 * - Chain with withStatus(), withCode(), withData().
 * - Pass the instance to ViewResponse for final rendering (JSON or HTML).
 */
final class ResponsePayload
{
    private bool $success;
    private ?int $status = null;
    private ?string $code = null;

    /** @var array<string,mixed>|null */
    private ?array $data = null;

    private function __construct(bool $success)
    {
        $this->success = $success;
    }

    /**
     * Factory for success payload.
     *
     * @return self
     */
    public static function success(): self
    {
        $payload = new self(true);
        return $payload;
    }

    /**
     * Factory for failure payload.
     *
     * @return self
     */
    public static function failure(): self
    {
        $payload = new self(false);
        return $payload;
    }

    /**
     * Attach numeric status (e.g., HTTP status code).
     *
     * @param int $status
     * @return self
     */
    public function withStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Attach semantic response code.
     *
     * @param string $code
     * @return self
     */
    public function withCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Attach arbitrary payload data.
     *
     * @param array<string,mixed> $data
     * @return self
     */
    public function withData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Export payload into flat array for rendering.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $result = [
            'success' => $this->success,
        ];

        if ($this->data !== null) {
            $result['data'] = $this->data;
        }

        if ($this->status !== null) {
            $result['status'] = $this->status;
        }

        if ($this->code !== null) {
            $result['code'] = $this->code;
        }

        return $result;
    }
}
