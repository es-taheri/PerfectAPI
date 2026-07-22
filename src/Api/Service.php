<?php

namespace PerfectAPI\Api;

use JSON\json;
use PerfectAPI\Api;

class Service extends Api
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function list(): Result
    {
        return $this->request('POST', '', ['action' => 'services']);
    }

    public function get(int|array $id): Result
    {
        $multi = is_array($id);
        $services = [];
        $result = $this->list();
        if ($result->ok() && $result->response()->success()) {
            $list = $result->response()->response();
            foreach ($list as $service):
                if ($multi ? in_array($service->service, $id) : $service->service == $id)
                    $services[$service->service] = $service;
            endforeach;
            if (!$multi):
                $services = array_values($services);
                $services = array_shift($services);
            endif;
            return new Result(['ok' => true, 'response' => json::_out($services)]);
        } else {
            return $result;
        }
    }
}
