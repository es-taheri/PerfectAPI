<?php

namespace PerfectAPI\AdminApi;

use Exception;
use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Filters\OrderList;
use PerfectAPI\AdminApi\Setters\UpdateOrder;

/**
 * Orders endpoints of the admin api.
 */
class Order extends AdminApi
{
    /**
     * Maximum number of items the api accepts per request.
     */
    public const MAX_LIMIT = 100;

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Pull pending orders of the manual services.
     * The api moves them to the Processing status after the request.
     * @param int|array|null $service_ids only pull orders of these services
     * @param int $limit maximum number of returned orders (100 at most)
     * @return Result
     */
    public function pull(int|array|null $service_ids = null, int $limit = self::MAX_LIMIT): Result
    {
        $params = ['limit' => self::limit($limit)];
        if (!empty($service_ids))
            $params['service_ids'] = Filters::to_comma($service_ids);
        return $this->request('POST', 'orders/pull', $params);
    }

    /**
     * Search and get a list of orders.
     * @param OrderList $filters
     * @return Result
     */
    public function list(OrderList $filters): Result
    {
        return $this->request('GET', 'orders', $filters->out());
    }

    /**
     * Get all available information about an order.
     * @param int $id order id
     * @return Result
     */
    public function order(int $id): Result
    {
        return $this->request('GET', "orders/$id");
    }

    /**
     * Get all available information about an order.
     * @param int $id order id
     * @return Result
     */
    public function get(int $id): Result
    {
        return $this->order($id);
    }

    /**
     * Update statuses, start count or remains of one or multiple orders.
     * @param array $updates array of UpdateOrder objects or raw arrays
     * @return Result
     * @throws Exception
     */
    public function update(array $updates): Result
    {
        $orders = [];
        foreach ($updates as $key => $update):
            $update = ($update instanceof UpdateOrder) ? $update->out() : $update;
            if (!is_array($update) || empty($update['id']))
                throw new Exception("Invalid order update on @$key index, id is required");
            $orders[] = $update;
        endforeach;
        if (empty($orders))
            throw new Exception('No order update was given');
        if (count($orders) > self::MAX_LIMIT)
            throw new Exception('Maximum ' . self::MAX_LIMIT . ' orders per request');
        return $this->request('POST', 'orders/update', ['orders' => $orders]);
    }

    /**
     * Change the order link.
     * @param int $id order id
     * @param string $link new order link
     * @return Result
     */
    public function edit_link(int $id, string $link): Result
    {
        return $this->request('POST', "orders/$id/edit-link", ['link' => $link]);
    }

    /**
     * Resend one or multiple fail orders.
     * @param int|array $ids order ids
     * @return Result
     */
    public function resend(int|array $ids): Result
    {
        return $this->request('POST', 'orders/resend', ['ids' => Filters::to_comma($ids)]);
    }

    /**
     * Change the status of one or multiple orders.
     * @param int|array $ids order ids
     * @param string $status new status, one of the Filters::STATUS_* constants
     * @return Result
     */
    public function change_status(int|array $ids, string $status): Result
    {
        return $this->request('POST', 'orders/change-status', ['ids' => Filters::to_comma($ids), 'status' => $status]);
    }

    /**
     * Set the remains of an order as partial.
     * @param int $id order id
     * @param int $remains quantity left to fulfill the order
     * @return Result
     */
    public function partial(int $id, int $remains): Result
    {
        return $this->request('POST', "orders/$id/set-partial", ['remains' => $remains]);
    }

    /**
     * Request cancellation of one or multiple orders (creates a cancel task).
     * @param int|array $ids order ids
     * @return Result
     */
    public function cancel(int|array $ids): Result
    {
        return $this->request('POST', 'orders/request-cancel', ['ids' => Filters::to_comma($ids)]);
    }

    /**
     * Cancel and refund one or multiple orders.
     * @param int|array $ids order ids
     * @param string|null $reason cancel reason shown to the users
     * @return Result
     */
    public function cancel_refund(int|array $ids, ?string $reason = null): Result
    {
        $params = ['ids' => Filters::to_comma($ids)];
        if (!empty($reason))
            $params['cancel_reason'] = $reason;
        return $this->request('POST', 'orders/cancel', $params);
    }

    /**
     * Keep the limit within the api bounds.
     * @param int $limit
     * @return int
     */
    private static function limit(int $limit): int
    {
        return max(1, min($limit, self::MAX_LIMIT));
    }
}
