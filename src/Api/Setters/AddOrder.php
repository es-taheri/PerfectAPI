<?php

namespace PerfectAPI\Api\Setters;

class AddOrder
{
    public static function default(int $service, string $link, int $quantity, int $runs = null, int $interval = null): array
    {
        return [
            'service' => $service,
            'link' => $link,
            'quantity' => $quantity,
            'runs' => $runs,
            'interval' => $interval,
        ];
    }

    public static function custom_comments(int $service, string $link, array $comments): array
    {
        return [
            'service' => $service,
            'link' => $link,
            'comments' => implode("\n", $comments)
        ];
    }

    public static function subscription(int $service, string $username, int $min, int $max, int $delay, int $posts = null, int $old_posts = null, string $expiry = null): array
    {
        return [
            'service' => $service,
            'username' => $username,
            'min' => $min,
            'max' => $max,
            'posts' => $posts,
            'old_posts' => $old_posts,
            'delay' => $delay,
            'expiry' => $expiry
        ];
    }
}