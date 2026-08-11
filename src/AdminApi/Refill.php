<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Setters\RefillTask;

class Refill extends AdminApi
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function pull(array|int $service_ids, int $limit = 10): Result
    {
        $service_ids = Filters::to_comma($service_ids);
        return $this->request('POST', 'refill/pull', ['service_ids' => $service_ids, 'limit' => $limit]);
    }

    /**
     * @param RefillTask[] $refill_tasks
     * @return Result
     * @throws \Exception
     */
    public function change_status(array $refill_tasks): Result
    {
        foreach ($refill_tasks as $key => $refill_task):
            if ($refill_task instanceof RefillTask)
                $refill_tasks[$key] = $refill_task->out();
            else
                throw new \Exception("Invalid refill task on @$key refill tasks");
        endforeach;
        return $this->request('POST', "refill/change-status");
    }
}