<?php

namespace PerfectAPI\AdminApi\Filters;

use AllowDynamicProperties;
use PerfectAPI\AdminApi\Filters;

/**
 * @method self ids(int|array $value)
 * @method self external_order_ids(int|array $value)
 * @method self created_from(int $value)
 * @method self created_to(int $value)
 * @method self order_status(string $value)
 * @method self mode(string $value)
 * @method self service_ids(int|array $value)
 * @method self creation_type(string $value)
 * @method self user(string $value)
 * @method self provider(string $value)
 * @method self ip_address(string $value)
 * @method self link(string $value)
 * @method self offset(int $value)
 * @method self limit(int $value)
 * @method self sort(string $value)
 */
#[AllowDynamicProperties]
class OrderList
{
    public int|array $ids;

    public int|array $external_order_ids;

    public int $created_from;

    public int $created_to;

    public string $order_status;

    public string $mode;

    public int|array $service_ids;

    public string $creation_type;

    public string $user;

    public string $provider;

    public string $ip_address;

    public string $link;

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