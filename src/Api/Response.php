<?php

namespace PerfectAPI\Api;

use Exception;
use JSON\json;

/**
 * The client api response envelope: `{error, ...data}`.
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
        $error = self::prop($this->response, 'error');
        if (is_null($error))
            return null;
        return is_scalar($error) ? (string)$error : json::to_json($error);
    }

    /**
     * Return the whole response payload (or one of its keys).
     * @param string $type type of the returned value (object, array, json)
     * @return object|array|string|null
     * @throws Exception
     */
    public function response(string $type = 'object'): object|array|string|null
    {
        return self::convert($this->response, $type);
    }

    /**
     * Return a single key of the response payload.
     * @param string $key
     * @param string $type type of the returned value (object, array, json)
     * @return object|array|string|null
     * @throws Exception
     */
    public function get(string $key, string $type = 'object'): object|array|string|null
    {
        return self::convert(self::prop($this->response, $key), $type);
    }

    /**
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
