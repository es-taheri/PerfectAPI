<?php

namespace PerfectAPI\AdminApi;

use Exception;
use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Setters\RejectCancel;

/**
 * Cancel tasks endpoints of the admin api.
 */
class Cancel extends AdminApi
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
     * Pull pending cancel tasks of the manual services.
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
        return $this->request('POST', 'cancel/pull', $params);
    }

    /**
     * Reject one or multiple manual cancel tasks.
     * @param RejectCancel|int|array $ids cancel task ids
     * @return Result
     * @throws Exception
     */
    public function reject(RejectCancel|int|array $ids): Result
    {
        $ids = ($ids instanceof RejectCancel) ? $ids->get(true) : Filters::to_comma($ids);
        if (empty($ids))
            throw new Exception('No cancel task id was given');
        return $this->request('POST', 'cancel/reject', ['ids' => $ids]);
    }
}
