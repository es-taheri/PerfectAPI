<?php

namespace PerfectAPI\Api\Setters;

/**
 * Request bodies of the `add` (order) action.
 */
class AddOrder
{
    /**
     * Default order.
     * @param int $service service id
     * @param string $link target link
     * @param int $quantity quantity
     * @param int|null $runs drip feed runs
     * @param int|null $interval drip feed interval in minutes
     * @return array
     */
    public static function default(int $service, string $link, int $quantity, ?int $runs = null, ?int $interval = null): array
    {
        return [
            'service' => $service,
            'link' => $link,
            'quantity' => $quantity,
            'runs' => $runs,
            'interval' => $interval,
        ];
    }

    /**
     * Custom comments order.
     * @param int $service service id
     * @param string $link target link
     * @param array $comments list of comments
     * @return array
     */
    public static function custom_comments(int $service, string $link, array $comments): array
    {
        return [
            'service' => $service,
            'link' => $link,
            'comments' => implode("\n", $comments)
        ];
    }

    /**
     * Subscription order.
     * @param int $service service id
     * @param string $username target username
     * @param int $min minimum quantity
     * @param int $max maximum quantity
     * @param int $delay delay in minutes
     * @param int|null $posts posts
     * @param int|null $old_posts old posts
     * @param string|null $expiry expiry date/time
     * @return array
     */
    public static function subscription(int $service, string $username, int $min, int $max, int $delay, ?int $posts = null, ?int $old_posts = null, ?string $expiry = null): array
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
