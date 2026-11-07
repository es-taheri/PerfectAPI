<?php

namespace PerfectAPI\AdminApi;

use Exception;
use JSON\json;

/**
 * The admin api response envelope: `{data, pagination, error_message, error_code}`.
 */
class Response
{
    private mixed $response;

    public function __construct(string $response)
    {
        $this->response = json::_in($response);
    }

    /**
     * Did the api report an error?
     * @return bool
     */
    public function success(): bool
    {
        return empty($this->error());
    }

    /**
     * Api error message.
     * @return string|null
     */
    public function error(): ?string
    {
        $message = $this->get('error_message');
        if (is_null($message))
            return null;
        return is_scalar($message) ? (string)$message : json::to_json($message);
    }

    /**
     * Api error code.
     * @return int|null
     */
    public function error_code(): ?int
    {
        $code = $this->get('error_code');
        return is_null($code) ? null : (int)$code;
    }

    /**
     * Return the response data.
     * @param string $type type of the returned value (object, array, json)
     * @return object|array|string|null
     * @throws Exception
     */
    public function data(string $type = 'object'): object|array|string|null
    {
        return self::convert($this->get('data'), $type);
    }

    /**
     * Return the response pagination block.
     * @param string $type type of the returned value (object, array, json)
     * @return object|array|string|null
     * @throws Exception
     */
    public function pagination(string $type = 'object'): object|array|string|null
    {
        return self::convert($this->get('pagination'), $type);
    }

    /**
     * Number of the returned items on the current page.
     * @return int
     */
    public function count(): int
    {
        $count = self::prop($this->get('data'), 'count');
        return is_null($count) ? 0 : (int)$count;
    }

    /**
     * The `data.list` array of a list response.
     * @return array
     */
    public function items(): array
    {
        $list = self::prop($this->get('data'), 'list');
        return is_array($list) ? $list : [];
    }

    /**
     * The whole response envelope.
     * @param string $type type of the returned value (object, array, json)
     * @return object|array|string|null
     * @throws Exception
     */
    public function raw(string $type = 'object'): object|array|string|null
    {
        return self::convert($this->response, $type);
    }

    /**
     * Read a top level key of the response envelope.
     * @param string $key
     * @return mixed
     */
    private function get(string $key): mixed
    {
        return self::prop($this->response, $key);
    }

    /**
     * Read a key of an object/array value.
     * @param mixed $data
     * @param string $key
     * @return mixed
     */
    private static function prop(mixed $data, string $key): mixed
    {
        if (is_object($data))
            return $data->$key ?? null;
        if (is_array($data))
            return $data[$key] ?? null;
        return null;
    }

    /**
     * @param mixed $data
     * @param string $to
     * @return object|array|string|null
     * @throws Exception
     */
    private static function convert(mixed $data, string $to): object|array|string|null
    {
        if (is_null($data))
            return null;
        return match ($to) {
            'json' => (string)json::to_json($data),
            'object' => json::to_object($data),
            'array' => json::to_array($data),
            default => throw new Exception("Unknown response convert type '$to'"),
        };
    }
}
