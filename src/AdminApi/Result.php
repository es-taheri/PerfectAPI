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
}
