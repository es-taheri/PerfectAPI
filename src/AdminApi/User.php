<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Filters\UserList;

class User extends AdminApi
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }
    public function list(UserList $filters): Result
    {
        return $this->request('GET', 'orders', $filters->out());
    }
}