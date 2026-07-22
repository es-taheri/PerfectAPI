<?php

namespace PerfectAPI\Api;

use PerfectAPI\Api\Filters\OrderStatus;
use PerfectAPI\Api\Filters\OrderCancel;
use PerfectAPI\Api\Filters\RefillStatus;

class Filters
{
    public const STATUS_Pending = 'Pending';
    public const STATUS_Active = 'Active';
    public const STATUS_InProgress = 'In progress';
    public const STATUS_Processing = 'Processing';
    public const STATUS_Completed = 'Completed';
    public const STATUS_Partial = 'Partial';
    public const STATUS_Cancelled = 'Canceled';
    public const STATUS_Awaiting = 'Awaiting';
    public const STATUS_Error = 'Error';
    public const STATUS_Fail = 'Fail';
    public const STATUS_Rejected = 'Rejected';
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