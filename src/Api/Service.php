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
        return $this->request('POST', '', array_merge(['action' => 'services']));
    }

    public function get(int|array $id): Result
    {
        $multi = is_array($id);
        $services = [];
        $result = $this->list();
        if ($result->ok() && $result->response()->success()) {
            $list = $result->response()->response();
            foreach ($list as $service):
                if ($multi ? in_array($service->id, $id) : $service->id == $id)
                    $services[$service->id] = $service;
            endforeach;
            if (!$multi)
                array_shift($services);
            return new Result(['ok' => true, 'response' => json::_out($services)]);
        } else {
            return $result;
        }
    }
}