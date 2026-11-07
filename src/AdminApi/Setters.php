<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi\Setters\AddPayment;
use PerfectAPI\AdminApi\Setters\AddTicket;
use PerfectAPI\AdminApi\Setters\AddUser;
use PerfectAPI\AdminApi\Setters\EditUser;
use PerfectAPI\AdminApi\Setters\RefillTask;
use PerfectAPI\AdminApi\Setters\RejectCancel;
use PerfectAPI\AdminApi\Setters\UpdateOrder;

/**
 * Factories for the admin api request bodies.
 */
class Setters
{
    public static function Order(): UpdateOrder
    {
        return new UpdateOrder();
    }

    public static function RejectCancel(int|array|null $ids = null): RejectCancel
    {
        return new RejectCancel($ids);
    }

    public static function RefillTask(): RefillTask
    {
        return new RefillTask();
    }

    public static function User(): AddUser
    {
        return new AddUser();
    }

    public static function EditUser(): EditUser
    {
        return new EditUser();
    }

    public static function Payment(): AddPayment
    {
        return new AddPayment();
    }

    public static function Ticket(): AddTicket
    {
        return new AddTicket();
    }

    public static function to_comma(mixed $array): string
    {
        return Filters::to_comma($array);
    }
}
