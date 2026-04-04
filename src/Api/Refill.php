<?php

namespace PerfectAPI\Api;

use PerfectAPI\Api;

class Refill extends Api
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function add(Setters\AddRefill $ids): Result
    {
        return $this->request('POST', '', ['action' => 'refill', $ids->multi() ? 'orders' : 'order' => $ids->get(true)]);
    }

    public function status(Api\Filters\RefillStatus $ids): Result
    {
        return $this->request('POST', '', ['action' => 'refill_status', $ids->multi() ? 'refills' : 'refill' => $ids->get(true)]);
    }
}
