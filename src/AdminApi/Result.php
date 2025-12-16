<?php

namespace PerfectAPI\AdminApi;

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

    public function response(bool $pure = false): Response|string
    {
        $response = $this->result['response'];
        if ($pure)
            return $response;
        else
            return new Response($response);
    }

    public function error(): string
    {
        return $this->result['error'];
    }
}