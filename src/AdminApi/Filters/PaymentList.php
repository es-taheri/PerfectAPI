<?php

namespace PerfectAPI\AdminApi\Filters;

use AllowDynamicProperties;
use PerfectAPI\AdminApi\Filters;

/**
 * Filters of the `GET /payments` endpoint.
 *
 * @method self ids(int|array $value)
 * @method self method(string|array $value)
 * @method self username(string $value)
 * @method self user_email(string $value)
 * @method self user_id(int $value)
 * @method self payment_status(string $value)
 * @method self ip_address(string $value)
 * @method self memo(string $value)
 * @method self created_from(int $value)
 * @method self created_to(int $value)
 * @method self mode(string $value)
 * @method self offset(int $value)
 * @method self limit(int $value)
 * @method self sort(string $value)
 */
#[AllowDynamicProperties]
class PaymentList
{
    public int|array $ids;

    public string|array $method;

    public string $username;

    public string $user_email;

    public int $user_id;

    public string $payment_status;

    public string $ip_address;

    public string $memo;

    public int $created_from;

    public int $created_to;

    public string $mode;

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
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function add(string $key, mixed $value): self
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

    /**
     * Build the query parameters, empty filters are skipped.
     * @return array
     */
    public function out(): array
    {
        $out = [];
        foreach (get_object_vars($this) as $key => $value):
            if (Filters::is_empty($value)) continue;
            $out[$key] = Filters::to_query($value);
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
