<?php

namespace PerfectAPI\AdminApi;

use JSON\json;

/**
 * Wrapper around a single admin api request result.
 */
class Result
{
    private array $result;

    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * Was the request sent and answered with a valid json body?
     * @return bool
     */
    public function ok(): bool
    {
        return !empty($this->result['ok']);
    }

    /**
     * Http status code of the response (if any).
     * @return int|null
     */
    public function status(): ?int
    {
        return $this->result['status'] ?? null;
    }

    /**
     * Get the response body.
     * @param bool $pure return the raw json string instead of a Response object
     * @return Response|string|null
     */
    public function response(bool $pure = false): Response|string|null
    {
        $response = $this->result['response'] ?? null;
        if ($pure || !json::_is($response))
            return $response;
        return new Response($response);
    }

    /**
     * Transport error message (connection error, timeout, ...).
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->result['error'] ?? null;
    }

    /**
     * The whole result array.
     * @return array
     */
    public function result(): array
    {
        return $this->result;
    }

    public static function make_ok(mixed $response = null): Result
    {
        return new self(['ok' => true, 'response' => $response]);
    }

    public static function make_fail(?string $error = null, mixed $response = null): Result
    {
        return new self(['ok' => false, 'error' => $error, 'response' => $response]);
    }

    public static function make_response(mixed $data): array
    {
        return ['data' => json::to_array($data)];
    }
}
