<?php

namespace PerfectAPI\Api;

use JSON\json;

class Response
{
    private object $response;

    public function __construct(string $response)
    {
        $this->response = json::_in($response);
    }

    public function success(): bool
    {
        return empty($this->response->error);
    }

    public function error(): string
    {
        return $this->response->error;
    }

    /**
     * return pure version of response data
     * @param string $type type of returned value (object,json,array)
     * @return object|array|string|null
     */
    public function response(string $type = 'object'): object|array|string|null
    {
        return self::convert($this->response, $type);
    }

    private static function convert($data, $to): object|array|string
    {
        return match ($to) {
            'json' => json::to_json($data),
            'object' => json::to_object($data),
            'array' => json::to_array($data),
        };
    }
}