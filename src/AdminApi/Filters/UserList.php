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
        foreach ($params as $key => $value):
            $this->$key = $value;
        endforeach;
    }

    /**
     * Add specific/custom set/sets.
     * @param $key
     * @param $value
     * @return self
     */
    public function add($key, $value): self
    {
        $this->$key = $value;
        return $this;
    }

    /**
     * Remove specific set/sets. \
     * To remove all sets don't set `$params`.
     * @param array|string $params
     * @return self
     */
    public function remove(array|string $params = []): self
    {
        if (empty($params)) {
            return new self();
        } else {
            if (is_string($params)) $params = [$params];
            foreach ($params as $key):
                unset($this->$key);
            endforeach;
            return $this;
        }
    }

    public function out(): array
    {
        $props = get_object_vars($this);
        $out = [];
        foreach ($props as $key => $value) :
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