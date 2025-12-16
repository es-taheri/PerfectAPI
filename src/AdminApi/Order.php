<?php

namespace PerfectAPI\AdminApi;

use Exception;
use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Filters\OrderList;
use PerfectAPI\AdminApi\Setters\UpdateOrder;

class Order extends AdminApi
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function pull(array|int $service_ids, int $limit = 10): Result
    {
        $service_ids = Filters::to_comma($service_ids);
        return $this->request('POST', 'orders/pull', ['service_ids' => $service_ids, 'limit' => $limit]);
    }

    public function list(OrderList $filters): Result
    {
        return $this->request('GET', 'orders', $filters->out());
    }

    public function order(int $id): Result
    {
        return $this->request('GET', "orders/$id");
    }

    /**
     * @throws Exception
     */
    public function update(array $updates): Result
    {
        foreach ($updates as $key => $update):
            if ($update instanceof UpdateOrder)
                $updates[$key] = $update->out();
            else
                throw new Exception("Invalid update on @$key update");
        endforeach;
        return $this->request('POST', "orders/update", $updates);
    }

    public function edit_link(int $id, string $link): Result
    {
        return $this->request('POST', "orders/$id/edit-link", ['link' => $link]);
    }

    public function resend(int|array $ids): Result
    {
        $ids = Filters::to_comma($ids);
        return $this->request('POST', "orders/resend", ['ids' => $ids]);
    }

    public function change_status(int|array $ids, string $status): Result
    {
        $ids = Filters::to_comma($ids);
        return $this->request('POST', "orders/change-status", ['ids' => $ids, 'status' => $status]);
    }

    public function partial(int $id, int $remains): Result
    {
        return $this->request('POST', "orders/$id/set-partial", ['remains' => $remains]);
    }

    public function cancel(int|array $ids): Result
    {
        $ids = Filters::to_comma($ids);
        return $this->request('POST', "orders/request-cancel", ['ids' => $ids]);
    }

    public function cancel_refund(int|array $ids, string $reason): Result
    {
        $ids = Filters::to_comma($ids);
        return $this->request('POST', "orders/cancel", ['ids' => $ids, 'reason' => $reason]);
    }
}