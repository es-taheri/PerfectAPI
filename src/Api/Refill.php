<?php

namespace PerfectAPI\Api;

use Exception;
use PerfectAPI\Api;
use PerfectAPI\Api\Filters\RefillStatus;
use PerfectAPI\Api\Setters\AddRefill;

/**
 * Refill actions of the client api.
 */
class Refill extends Api
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Create a refill for one or multiple orders.
     * @param AddRefill|int|array $ids order id bag
     * @return Result
     * @throws Exception
     */
    public function add(AddRefill|int|array $ids): Result
    {
        $multi = ($ids instanceof AddRefill) ? $ids->multi() : is_array($ids);
        $values = ($ids instanceof AddRefill) ? $ids->get(true) : Filters::to_comma($ids);
        if (empty($values))
            throw new Exception('No order id was given');
        return $this->request('POST', '', ['action' => 'refill', $multi ? 'orders' : 'order' => $values]);
    }

    /**
     * Get the status of one or multiple refills.
     * @param RefillStatus $ids refill id bag
     * @return Result
     * @throws Exception
     */
    public function status(RefillStatus $ids): Result
    {
        if ($ids->is_empty())
            throw new Exception('No refill id was given');
        return $this->request('POST', '', ['action' => 'refill_status', $ids->multi() ? 'refills' : 'refill' => $ids->get(true)]);
    }
}
