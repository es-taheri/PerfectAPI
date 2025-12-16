<?php

namespace PerfectAPI\AdminApi\Setters;

use PerfectAPI\AdminApi\Filters;

/**
 * @method self id(int $value)
 * @method self status(string $value)
 * @method self remains(int $value)
 * @method self start_count(int $value)
 * @method self cancel_reason(string $value)
 */
class UpdateOrder
{
    public int $id;
    public string $status;
    public int $remains;
    public int $start_count;
    public string $cancel_reason;

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
     * Remove specific set/s. \
     * To remove all sets don't set `$params`.
     * @param array|string $params
     * @return UpdateOrder
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
        $keys = ['id', 'status', 'remains', 'start_count', 'cancel_reason'];
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