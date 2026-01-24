<?php

namespace PerfectAPI\AdminApi\Filters;

use PerfectAPI\AdminApi\Filters;

/**
 * @method self ids(int|array $value)
 * @method self username(string $value)
 * @method self email(string $value)
 * @method self ip_addresses(string|array $value)
 * @method self status(string $value)
 * @method self offset(int $value)
 * @method self limit(int $value)
 * @method self sort(string $value)
 */
class UserList
{
    public int|array $ids;
    public string $username;
    public string $email;
    public string|array $ip_addresses;
    public string $status;
    public int $offset;

    public int $limit;

    public string $sort;

    public function __construct(array $params = [])
    {
        $keys = [
            'ids',
            'username',
            'email',
            'ip_addresses',
            'status',
            'offset',
            'limit',
            'sort',
        ];
        foreach ($keys as $key):
            if (array_key_exists($key, $params))
                $this->$key = $params[$key];
        endforeach;
    }

    /**
     * Remove specific filter/s. \
     * To remove all filters don't set `$params`.
     * @param array|string $params
     * @return UserList
     */
    public function remove(array|string $params = []): self
    {
        if (empty($params))
            return new self();
        else {
            foreach ((array)$params as $key):
                unset($this->$key);
            endforeach;
            return $this;
        }
    }

    public function out(): array
    {
        $keys = [
            'ids',
            'username',
            'email',
            'ip_addresses',
            'status',
            'offset',
            'limit',
            'sort',
        ];
        $out = [];
        foreach ($keys as $key) :
            if (!isset($this->$key))
                continue;
            $value = $this->$key;
            $value = Filters::to_comma($value);
            $out[$key] = $value;
        endforeach;
        return $out;
    }

    public function __call(string $name, array $arguments)
    {
        $value = array_shift($arguments);
        $this->$name = $value;
        return $this;
    }
}