<?php

namespace PerfectAPI\Api\Setters;

use PerfectAPI\Api\Setters;

class AddRefill
{
    public function __construct(private int|array $ids)
    {
    }

    public function add(int|array $ids): static
    {
        if (is_array($this->ids))
            $this->ids = array_values(array_unique(array_merge($ids, $this->ids)));
        else
            $this->ids = is_array($ids) ? array_values($ids) : $ids;
        return $this;
    }

    public function exists(int|array $ids): bool
    {
        return is_array($this->ids) ? in_array($ids, $this->ids) : $this->ids == $ids;
    }

    public function get(bool $comma = false): int|array|string
    {
        return $comma ? Setters::to_comma($this->ids) : $this->ids;
    }

    public function remove(int|array $ids): static
    {
        if (is_array($ids)) {
            foreach ($ids as $id):
                unset($this->ids[$id]);
            endforeach;
        } else {
            $this->ids = [];
        }
        return $this;
    }

    public function multi(): bool
    {
        return is_array($this->ids);
    }
}
