<?php
declare(strict_types=1);

namespace Daylog\Presentation\Views;

use Daylog\Application\Responses\UseCaseResponseInterface;
use InvalidArgumentException;

/**
 * ViewResponse accumulates payloads from controllers and
 * renders them as HTTP responses (HTML or JSON).
 *
 * Purpose:
 * - Provide a single object for controllers to set data into.
 * - Normalize different input types (UseCaseResponse vs. plain array).
 * - Emit consistent output in afterroute().
 *
 * Mechanics:
 * - setJson() accepts UseCaseResponseInterface and stores as array.
 * - setHtml() accepts an array of template data.
 * - render() inspects type and outputs the final string with headers.
 */
final class ViewResponse
{
    /** @var array{type:string,data:array<string,mixed>}|null */
    private ?array $payload = null;

    /**
     * Accept UseCaseResponse for JSON API responses.
     *
     * @param UseCaseResponseInterface $response
     * @return void
     */
    public function setJson(UseCaseResponseInterface $response): void
    {
        $this->payload = [
            'type' => 'json',
            'data' => $response->toArray(),
        ];
    }

    /**
     * Accept array data for HTML page rendering.
     *
     * @param array<string,mixed> $data
     * @return void
     */
    public function setHtml(array $data): void
    {
        $this->payload = [
            'type' => 'html',
            'data' => $data,
        ];
    }

    /**
     * Render the final response.
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->payload === null) {
            throw new InvalidArgumentException('No payload set in ViewResponse.');
        }

        if ($this->payload['type'] === 'json') {
            header('Content-Type: application/json');
            return json_encode($this->payload['data'], JSON_UNESCAPED_UNICODE);
        }

        if ($this->payload['type'] === 'html') {
            header('Content-Type: text/html; charset=utf-8');
            // TODO: integrate with real template engine.
            return '<pre>' . htmlspecialchars(print_r($this->payload['data'], true)) . '</pre>';
        }

        throw new InvalidArgumentException("Unsupported response type: {$this->payload['type']}");
    }
}
