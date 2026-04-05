<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi;

class Ticket extends AdminApi
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function add(string $staff, string $subject, string $message, string $username_or_email): Result
    {
        return $this->request('POST', 'tickets/add', [
            'staff_name' => $staff,
            'subject' => $subject,
            'message' => $message,
            (str_contains($username_or_email, '@')) ? 'user_email' : 'username' => $username_or_email
        ]);
    }
}
