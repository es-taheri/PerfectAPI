<?php

namespace PerfectAPI\AdminApi\Setters;

use AllowDynamicProperties;
use PerfectAPI\AdminApi\Filters;

/**
 * One refill task of the `POST /refill/change-status` array.
 *
 * @method self id(int $value)
 * @method self status(string $value)
 */
#[AllowDynamicProperties]
class RefillTask
{
    public int $id;
    public string $status;

    /**
     * Allowed refill task statuses.
     */
    public const STATUS_Completed = 'completed';
    public const STATUS_Rejected = 'rejected';

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
     * Build the refill task body, empty values are skipped.
     * @return array
     */
    public function out(): array
    {
        $out = [];
        foreach (get_object_vars($this) as $key => $value):
            if (Filters::is_empty($value)) continue;
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
