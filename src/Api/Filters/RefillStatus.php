<?php

namespace PerfectAPI\Api\Filters;

use PerfectAPI\Api\Filters;

/**
 * Refill id bag of the `refill_status` action.
 */
class RefillStatus
{
    public function __construct(private int|array|null $ids = null)
    {
    }

    /**
     * Add one or multiple refill ids.
     * @param int|array $ids
     * @return static
     */
    public function add(int|array $ids): static
    {
        if (is_array($this->ids))
            $this->ids = array_values(array_unique(array_merge((array)$ids, $this->ids)));
        else
            $this->ids = is_array($ids) ? array_values(array_unique($ids)) : $ids;
        return $this;
    }

    /**
     * Is the given id in the bag?
     * @param int|array $ids
     * @return bool
     */
    public function exists(int|array $ids): bool
    {
        if (is_array($ids)):
            foreach ($ids as $id):
                if (!$this->exists($id)) return false;
            endforeach;
            return true;
        endif;
        return is_array($this->ids) ? in_array($ids, $this->ids) : $this->ids == $ids;
    }

    /**
     * Get the ids.
     * @param bool $comma return a comma separated string
     * @return int|array|string
     */
    public function get(bool $comma = false): int|array|string
    {
        return $comma ? Filters::to_comma($this->ids) : $this->ids;
    }

    /**
     * Remove one or multiple ids.
     * @param int|array $ids
     * @return static
     */
    public function remove(int|array $ids): static
    {
        $remove = is_array($ids) ? $ids : [$ids];
        if (is_array($this->ids)) {
            $this->ids = array_values(array_diff($this->ids, $remove));
            if (empty($this->ids)) $this->ids = null;
        } elseif (in_array($this->ids, $remove)) {
            $this->ids = null;
        }
        return $this;
    }

    /**
     * Does the bag hold multiple ids?
     * @return bool
     */
    public function multi(): bool
    {
        return is_array($this->ids);
    }

    /**
     * Is the bag empty?
     * @return bool
     */
    public function is_empty(): bool
    {
        return empty($this->ids);
    }
}
