<?php

namespace PerfectAPI\AdminApi\Filters;

use AllowDynamicProperties;
use PerfectAPI\AdminApi\Filters;

/**
 * Filters of the `GET /tickets` endpoint.
 *
 * @method self ids(int|array $value)
 * @method self message(string $value)
 * @method self user(string $value)
 * @method self assignee(string $value)
 * @method self status(string $value)
 * @method self is_read(bool $value)
 * @method self offset(int $value)
 * @method self limit(int $value)
 */
#[AllowDynamicProperties]
class TicketList
{
    public int|array $ids;

    public string $message;

    public string $user;

    public string $assignee;

    public string $status;

    public bool $is_read;

    public int $offset;

    public int $limit;

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
