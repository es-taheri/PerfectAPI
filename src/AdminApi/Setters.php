<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi\Setters\UpdateOrder;

class Setters
{
    public static function Order(): UpdateOrder
    {
        return new UpdateOrder();
    }

    public static function to_comma(mixed $array): string
    {
        return (is_array($array)) ? implode(',', $array) : $array;
    }

}