<?php

namespace PerfectAPI\Api;

use JSON\json;

class Result
{
    private array $result;

    public function __construct(array $result)
    {
        $this->result = $result;
    }

    public function ok(): bool
    {
        return $this->result['ok'];
    }

    public function response(bool $pure = false): Response|string|null
    {
        $response = $this->result['response'];
        if ($pure || !json::_is($response))
            return $response;
        else
            return new Response($response);
    }

    public function error(): string
    {
        return $this->result['error'];
    }

    public static function make_ok(array $response = null): Result
    {
        return new self(['ok' => true, 'response' => $response]);
    }

    public static function make_fail(string $error, array $response = null): Result
    {
        return new self(['ok' => false, 'error' => $error, 'response' => $response]);
    }

}
