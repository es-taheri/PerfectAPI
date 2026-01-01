<?php

namespace PerfectAPI\Api;

use PerfectAPI\Api\Filters\OrderStatus;
use PerfectAPI\Api\Filters\OrderCancel;
use PerfectAPI\Api\Filters\RefillStatus;

class Filters
{
    public static function Status(int|array $ids): OrderStatus
    {
        return new OrderStatus($ids);
    }

    public static function Cancel(int|array $ids): OrderCancel
    {
        return new OrderCancel($ids);
    }

    public static function Refill(int|array $ids): RefillStatus
    {
        return new RefillStatus($ids);
    }

    public static function to_comma(mixed $array): string
    {
        return (is_array($array)) ? implode(',', $array) : $array;
    }
}