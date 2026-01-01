<?php

namespace PerfectAPI\Api;

use PerfectAPI\Api\Setters\AddOrder;
use PerfectAPI\Api\Setters\AddRefill;

class Setters
{
    public static function Order(): AddOrder
    {
        return new AddOrder();
    }

    public static function Refill(int|array $ids): AddRefill
    {
        return new AddRefill($ids);
    }

    public static function to_comma(mixed $array): string
    {
        return (is_array($array)) ? implode(',', $array) : $array;
    }

}