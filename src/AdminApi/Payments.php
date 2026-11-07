<?php

namespace PerfectAPI\AdminApi;

use Exception;
use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Filters\PaymentList;
use PerfectAPI\AdminApi\Setters\AddPayment;

/**
 * Payments endpoints of the admin api.
 */
class Payments extends AdminApi
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Search and get a list of payments.
     * @param PaymentList $filters
     * @return Result
     */
    public function list(PaymentList $filters): Result
    {
        return $this->request('GET', 'payments', $filters->out());
    }

    /**
     * Add a payment for a panel user.
     * @param AddPayment|array $payment
     * @return Result
     * @throws Exception
     */
    public function add(AddPayment|array $payment): Result
    {
        $params = is_array($payment) ? $payment : $payment->out();
        self::check($params, ['method', 'amount']);
        self::check_one($params, ['user_id', 'username', 'user_email']);
        return $this->request('POST', 'payments/add', $params);
    }
}
