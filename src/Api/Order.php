<?php

namespace PerfectAPI\Api;

use Exception;
use PerfectAPI\Api;
use PerfectAPI\Api\Filters\OrderCancel;
use PerfectAPI\Api\Filters\OrderStatus;

/**
 * Order actions of the client api.
 */
class Order extends Api
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Create a new order.
     * @param array $params order parameters, see `Setters\AddOrder`
     * @return Result
     * @throws Exception
     */
    public function add(array $params): Result
    {
        self::check($params, ['service', 'link', 'quantity']);
        return $this->request('POST', '', array_merge(['action' => 'add'], $params));
    }

    /**
     * Get the status of one or multiple orders.
     * @param OrderStatus $ids order id bag
     * @return Result
     * @throws Exception
     */
    public function status(OrderStatus $ids): Result
    {
        if ($ids->is_empty())
            throw new Exception('No order id was given');
        return $this->request('POST', '', ['action' => 'status', $ids->multi() ? 'orders' : 'order' => $ids->get(true)]);
    }

    /**
     * Cancel one or multiple orders.
     * @param OrderCancel $ids order id bag
     * @return Result
     * @throws Exception
     */
    public function cancel(OrderCancel $ids): Result
    {
        if ($ids->is_empty())
            throw new Exception('No order id was given');
        return $this->request('POST', '', ['action' => 'cancel', $ids->multi() ? 'orders' : 'order' => $ids->get(true)]);
    }
}
