<?php

namespace PerfectAPI\Api\Filters;

class OrderStatus
{
    public function __construct(private int|array|null $ids)
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

    public function get(bool $comma = true): int|array
    {
        return is_array($this->ids) ;
    }

    public function remove(int|array $ids): static
    {
        if (is_array($ids)) {
            foreach ($ids as $id):
                unset($this->ids[$id]);
            endforeach;
        } else {
            $this->ids = null;
        }
        return $this;
    }

    public function multi(): bool
    {
        return is_array($this->ids);
    }
}