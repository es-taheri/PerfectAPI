<?php

namespace PerfectAPI\AdminApi\Setters;

use AllowDynamicProperties;
use PerfectAPI\AdminApi\Filters;

/**
 * @method self id(int $value)
 * @method self status(string $value)
 */
#[AllowDynamicProperties]
class RefillTask
{
    public int $id;
    public string $status;

    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value):
            $this->$key = $value;
        endforeach;
    }

    /**
     * Add specific/custom set/sets.
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
     * @return UpdateOrder
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