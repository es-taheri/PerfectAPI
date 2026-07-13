<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Setters\RejectCancel;

class Cancel extends AdminApi
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function pull(array|int $service_ids, int $limit = 10): Result
    {
        $service_ids = Filters::to_comma($service_ids);
        return $this->request('POST', 'cancel/pull', ['service_ids' => $service_ids, 'limit' => $limit]);
    }

    public function reject(RejectCancel $ids): Result
    {
        return $this->request('POST', 'cancel/reject', ['ids' => $ids->get(true)]);
    }

}