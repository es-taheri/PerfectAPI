<?php

namespace PerfectAPI\AdminApi;

use Exception;
use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Filters\UserList;
use PerfectAPI\AdminApi\Setters\AddUser;
use PerfectAPI\AdminApi\Setters\EditUser;

/**
 * Users endpoints of the admin api.
 */
class User extends AdminApi
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Search and get a list of users.
     * @param UserList $filters
     * @return Result
     */
    public function list(UserList $filters): Result
    {
        return $this->request('GET', 'users', $filters->out());
    }

    /**
     * Get a single user by its id.
     * @param int $id user id
     * @return Result
     */
    public function get(int $id): Result
    {
        return $this->request('GET', 'users', ['ids' => $id]);
    }

    /**
     * Add a user to the panel.
     * @param AddUser|array $user
     * @return Result
     * @throws Exception
     */
    public function add(AddUser|array $user): Result
    {
        $params = is_array($user) ? $user : $user->out();
        self::check($params, ['username', 'email', 'password']);
        if (($params['allowed_methods'] ?? null) === AddUser::METHODS_Custom)
            self::check($params, ['custom_list']);
        return $this->request('POST', 'users/add', $params);
    }

    /**
     * Edit a user and manage its access to payment methods and services.
     * @param int $id user id
     * @param EditUser|array $user
     * @return Result
     * @throws Exception
     */
    public function edit(int $id, EditUser|array $user): Result
    {
        $params = is_array($user) ? $user : $user->out();
        if (empty($params))
            throw new Exception('No user field was given to update');
        return $this->request('POST', "users/$id/edit", $params);
    }
}
