<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi\Filters\OrderList;

class Filters
{
    public static function Order(): OrderList
    {
        return new OrderList();
    }

    public static function to_comma(mixed $array): string
    {
        return (is_array($array)) ? implode(',', $array) : $array;
    }
}