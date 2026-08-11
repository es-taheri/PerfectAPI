<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi\Setters\RefillTask;
use PerfectAPI\AdminApi\Setters\RejectCancel;
use PerfectAPI\AdminApi\Setters\UpdateOrder;

class Setters
{
    public static function Order(): UpdateOrder
    {
        return new UpdateOrder();
    }

    public static function RejectCancel(int|array $ids=[]): RejectCancel
    {
        return new RejectCancel($ids);
    }
    public static function RefillTask(): RefillTask
    {
        return new RefillTask();
    }
    public static function to_comma(mixed $array): string
    {
        return (is_array($array)) ? implode(',', $array) : $array;
    }

}