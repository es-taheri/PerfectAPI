<?php

namespace PerfectAPI\AdminApi\Setters;

use AllowDynamicProperties;
use PerfectAPI\AdminApi\Filters;

/**
 * Body of the `POST /payments/add` endpoint.
 *
 * The user is identified by exactly one of `user_id`, `username` or
 * `user_email`, use one of the `by_*` named constructors.
 *
 * @method self user_id(int $value)
 * @method self username(string $value)
 * @method self user_email(string $value)
 * @method self amount(float|int $value)
 * @method self method(string $value)
 * @method self memo(string|null $value)
 * @method self affiliate_commission(bool $value)
 */
#[AllowDynamicProperties]
class AddPayment
{
    public int $user_id;
    public string $username;
    public string $user_email;
    public float|int $amount;
    public string $method;
    public ?string $memo;
    public bool $affiliate_commission;

    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value):
            $this->$key = $value;
        endforeach;
    }

    /**
     * Build a payment for a user id.
     * @param int $user_id
     * @param float|int $amount payment amount in panel currency
     * @param string $method payment method shown in the payment details
     * @param string|null $memo payment memo shown to the staff
     * @param bool $affiliate_commission include the payment in the affiliate commission
     * @return self
     */
    public static function by_id(int $user_id, float|int $amount, string $method, ?string $memo = null, bool $affiliate_commission = false): self
    {
        return (new self(['user_id' => $user_id, 'amount' => $amount, 'method' => $method, 'affiliate_commission' => $affiliate_commission]))
            ->memo($memo);
    }

    /**
     * Build a payment for a username.
     * @param string $username
     * @param float|int $amount payment amount in panel currency
     * @param string $method payment method shown in the payment details
     * @param string|null $memo payment memo shown to the staff
     * @param bool $affiliate_commission include the payment in the affiliate commission
     * @return self
     */
    public static function by_username(string $username, float|int $amount, string $method, ?string $memo = null, bool $affiliate_commission = false): self
    {
        return (new self(['username' => $username, 'amount' => $amount, 'method' => $method, 'affiliate_commission' => $affiliate_commission]))
            ->memo($memo);
    }

    /**
     * Build a payment for a user email.
     * @param string $user_email
     * @param float|int $amount payment amount in panel currency
     * @param string $method payment method shown in the payment details
     * @param string|null $memo payment memo shown to the staff
     * @param bool $affiliate_commission include the payment in the affiliate commission
     * @return self
     */
    public static function by_email(string $user_email, float|int $amount, string $method, ?string $memo = null, bool $affiliate_commission = false): self
    {
        return (new self(['user_email' => $user_email, 'amount' => $amount, 'method' => $method, 'affiliate_commission' => $affiliate_commission]))
            ->memo($memo);
    }

    /**
     * Add specific/custom set/sets.
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function add(string $key, mixed $value): self
    {
        $this->$key = $value;
        return $this;
    }

    /**
     * Remove specific set/sets. \
     * To remove all sets don't set `$params`.
     * @param array|string $params
     * @return self
     */
    public function remove(array|string $params = []): self
    {
        if (empty($params)) {
            return new self();
        } else {
            if (is_string($params)) $params = [$params];
            foreach ($params as $key):
                unset($this->$key);
            endforeach;
            return $this;
        }
    }

    /**
     * Build the payment body, empty values are skipped.
     * @return array
     */
    public function out(): array
    {
        $out = [];
        foreach (get_object_vars($this) as $key => $value):
            if (Filters::is_empty($value)) continue;
            $out[$key] = $value;
        endforeach;
        return $out;
    }

    public function __call(string $name, array $arguments)
    {
        $value = array_shift($arguments);
        $this->$name = $value;
        return $this;
    }
}
