<?php

namespace PerfectAPI\Api;

use JSON\json;
use PerfectAPI\Api;

/**
 * Service actions of the client api.
 */
class Service extends Api
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Get the list of all services.
     * @return Result
     */
    public function list(): Result
    {
        return $this->request('POST', '', ['action' => 'services']);
    }

    /**
     * Get one or multiple services of the list.
     * @param int|array $id service id or an array of service ids
     * @return Result
     */
    public function get(int|array $id): Result
    {
        $multi = is_array($id);
        $services = [];
        $result = $this->list();
        if (!$result->ok())
            return $result;
        $response = $result->response();
        if (!$response instanceof Response || !$response->success())
            return $result;
        $list = $response->response();
        if (!is_iterable($list))
            return Result::make_fail('Unexpected services response');
        foreach ($list as $service):
            if ($multi ? in_array($service->service, $id) : $service->service == $id)
                $services[$service->service] = $service;
        endforeach;
        if (!$multi):
            $services = array_values($services);
            if (empty($services))
                return Result::make_fail('Service not found');
            $services = array_shift($services);
        endif;
        return new Result(['ok' => true, 'response' => json::_out($services)]);
    }
}
