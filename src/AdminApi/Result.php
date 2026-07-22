<?php

namespace PerfectAPI\AdminApi;

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

    public static function make_fail(int $code, string $error, array $response = null): Result
    {
        return new self(['ok' => false, 'code' => $code, 'error' => $error, 'response' => $response]);
    }

    public static function make_response(bool $success, array|object|string $obj, string $msg = ''): array
    {
        return ['success' => $success, 'obj' => $obj, 'msg' => $msg];
    }

}
