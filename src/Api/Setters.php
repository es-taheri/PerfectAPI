<?php

namespace PerfectAPI\Api;

use PerfectAPI\Api\Setters\AddOrder;
use PerfectAPI\Api\Setters\AddRefill;

/**
 * Factories for the api request bodies.
 */
class Setters
{
    public static function Order(): AddOrder
    {
        return new AddOrder();
    }

    public static function Refill(int|array|null $ids = null): AddRefill
    {
        return new AddRefill($ids);
    }

    public static function to_comma(mixed $array): string
    {
        return Filters::to_comma($array);
    }
}
