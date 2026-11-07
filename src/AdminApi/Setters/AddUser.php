<?php

namespace PerfectAPI\AdminApi\Setters;

use AllowDynamicProperties;
use PerfectAPI\AdminApi\Filters;

/**
 * Body of the `POST /users/add` endpoint.
 *
 * @method self username(string $value)
 * @method self email(string $value)
 * @method self password(string $value)
 * @method self first_name(string $value)
 * @method self last_name(string $value)
 * @method self phone(string $value)
 * @method self whatsapp(string $value)
 * @method self telegram(string $value)
 * @method self website(string $value)
 * @method self skype(string $value)
 * @method self allowed_methods(string $value)
 * @method self custom_list(array $value)
 */
#[AllowDynamicProperties]
class AddUser
{
    public const METHODS_All = 'all';
    public const METHODS_None = 'none';
    public const METHODS_NewUsers = 'new_users';
    public const METHODS_Custom = 'custom';

    public string $username;
    public string $email;
    public string $password;
    public string $first_name;
    public string $last_name;
    public string $phone;
    public string $whatsapp;
    public string $telegram;
    public string $website;
    public string $skype;
    public string $allowed_methods;
    public array $custom_list;

    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value):
            $this->$key = $value;
        endforeach;
    }

    /**
     * Allow only the given payment method ids.
     * @param array $ids
     * @return self
     */
    public function custom(array $ids): self
    {
        $this->allowed_methods = self::METHODS_Custom;
        $this->custom_list = array_values($ids);
        return $this;
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
     * Build the user body, empty values are skipped.
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
