<?php

namespace PerfectAPI\AdminApi;

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
        return empty($this->response->error_message);
    }

    public function error(): string
    {
        return $this->response->error_message;
    }

    /**
     * return pure version of response data
     * @param string $type type of returned value (object,json,array)
     * @return object|array|string|null
     */
    public function data(string $type = 'object'): object|array|string|null
    {
        return isset($this->response->data) ? self::convert($this->response->data, $type) : null;
    }

    /**
     * return pure version of response pagination
     * @param string $type type of returned value (object,json,array)
     * @return object|array|string|null
     */
    public function pagination(string $type = 'object'): object|array|string|null
    {
        return isset($this->response->pagination) ? self::convert($this->response->pagination, $type) : null;
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