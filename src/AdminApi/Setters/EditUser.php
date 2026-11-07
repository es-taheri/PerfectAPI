<?php

namespace PerfectAPI\AdminApi\Setters;

use AllowDynamicProperties;

/**
 * Body of the `POST /users/{user_id}/edit` endpoint.
 *
 * Every field is nullable, so `null` means "clear the stored value" while an
 * unset field is simply not sent to the api.
 *
 * @method self username(string $value)
 * @method self email(string $value)
 * @method self first_name(string|null $value)
 * @method self last_name(string|null $value)
 * @method self phone(string|null $value)
 * @method self whatsapp(string|null $value)
 * @method self telegram(string|null $value)
 * @method self website(string|null $value)
 * @method self skype(string|null $value)
 * @method self invoice_details(string|null $value)
 * @method self hidden_services(string $value)
 * @method self allow_methods(array $value)
 * @method self disallow_methods(array $value)
 */
#[AllowDynamicProperties]
class EditUser
{
    public const HIDDEN_Allowed = 'allowed';
    public const HIDDEN_NotAllowed = 'not_allowed';

    public ?string $username;
    public ?string $email;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $phone;
    public ?string $whatsapp;
    public ?string $telegram;
    public ?string $website;
    public ?string $skype;
    public ?string $invoice_details;
    public ?string $hidden_services;
    public ?array $allow_methods;
    public ?array $disallow_methods;

    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value):
            $this->$key = $value;
        endforeach;
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
     * Build the user body, only the fields that were set are returned.
     * @return array
     */
    public function out(): array
    {
        return get_object_vars($this);
    }

    public function __call(string $name, array $arguments)
    {
        $value = array_shift($arguments);
        $this->$name = $value;
        return $this;
    }
}
