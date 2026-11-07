<?php

namespace PerfectAPI\Api;

use PerfectAPI\Api\Filters\OrderCancel;
use PerfectAPI\Api\Filters\OrderStatus;
use PerfectAPI\Api\Filters\RefillStatus;

/**
 * Factories for the api id bags, status constants and the comma separated helper.
 */
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

    /**
     * Convert an id/value or an array of them to the api's comma separated format.
     * @param mixed $value
     * @return string
     */
    public static function to_comma(mixed $value): string
    {
        if (is_array($value))
            return implode(',', $value);
        return is_null($value) ? '' : (string)$value;
    }
}
