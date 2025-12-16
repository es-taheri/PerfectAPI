<?php

namespace PerfectAPI\AdminApi\Filters;

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
        $keys = [
            'ids',
            'external_order_ids',
            'created_from',
            'created_to',
            'order_status',
            'mode',
            'service_ids',
            'creation_type',
            'user',
            'provider',
            'ip_address',
            'link',
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
     * @return OrderList
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
            'ids', 'external_order_ids', 'created_from', 'created_to', 'order_status',
            'mode', 'service_ids', 'creation_type', 'user', 'provider', 'ip_address',
            'link', 'offset', 'limit', 'sort'
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