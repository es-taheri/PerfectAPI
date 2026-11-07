<?php

namespace PerfectAPI\AdminApi;

use Exception;
use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Setters\RefillTask;

/**
 * Refill tasks endpoints of the admin api.
 */
class Refill extends AdminApi
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
     * Pull pending manual refill tasks.
     * The api moves them to the Awaiting status after the request.
     * @param int|array|null $service_ids only pull tasks of these services
     * @param int $limit maximum number of returned tasks (100 at most)
     * @return Result
     */
    public function pull(int|array|null $service_ids = null, int $limit = self::MAX_LIMIT): Result
    {
        $params = ['limit' => max(1, min($limit, self::MAX_LIMIT))];
        if (!empty($service_ids))
            $params['service_ids'] = Filters::to_comma($service_ids);
        return $this->request('POST', 'refill/pull', $params);
    }

    /**
     * Change the status of one or multiple refill tasks.
     * @param array $refill_tasks array of RefillTask objects or raw arrays
     * @return Result
     * @throws Exception
     */
    public function change_status(array $refill_tasks): Result
    {
        $tasks = [];
        foreach ($refill_tasks as $key => $refill_task):
            $refill_task = ($refill_task instanceof RefillTask) ? $refill_task->out() : $refill_task;
            if (!is_array($refill_task) || empty($refill_task['id']))
                throw new Exception("Invalid refill task on @$key index, id is required");
            $tasks[] = $refill_task;
        endforeach;
        if (empty($tasks))
            throw new Exception('No refill task was given');
        if (count($tasks) > self::MAX_LIMIT)
            throw new Exception('Maximum ' . self::MAX_LIMIT . ' refill tasks per request');
        return $this->request('POST', 'refill/change-status', ['refill_tasks' => $tasks]);
    }
}
