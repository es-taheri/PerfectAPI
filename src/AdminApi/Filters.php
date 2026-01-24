<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi\Filters\OrderList;

class Filters
{
    public const STATUS_Pending = 'pending';
    public const STATUS_InProgress = 'in_progress';
    public const STATUS_Processing = 'processing';
    public const STATUS_Completed = 'completed';
    public const STATUS_Partial = 'partial';
    public const STATUS_Canceled = 'canceled';
    public const STATUS_Fail = 'fail';
    public const STATUS_Error = 'error';

    public static function Order(): OrderList
    {
        return new OrderList();
    }

    public static function to_comma(mixed $array): string
    {
        return (is_array($array)) ? implode(',', $array) : $array;
    }
}