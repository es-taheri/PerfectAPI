<?php

namespace PerfectAPI\Api;

use PerfectAPI\Api;

class Order extends Api
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function add(array $params): Result
    {
        return $this->request('POST', '', array_merge(['action' => 'add'], $params));
    }

    public function status(Api\Filters\OrderStatus $ids): Result
    {
        return $this->request('POST', '', ['action' => 'status', $ids->multi() ? 'orders' : 'order' => $ids->get(true)]);
    }

    public function cancel(Api\Filters\OrderCancel $ids): Result
    {
        return $this->request('POST', '', ['action' => 'cancel', $ids->multi() ? 'orders' : 'order' => $ids->get(true)]);
    }
}
