<?php

namespace PerfectAPI\AdminApi\Setters;

use AllowDynamicProperties;
use PerfectAPI\AdminApi\Filters;

/**
 * Body of the `POST /tickets/add` endpoint.
 *
 * The user is identified by exactly one of `username` or `user_email`,
 * use one of the `by_*` named constructors.
 *
 * @method self staff_name(string|null $value)
 * @method self subject(string $value)
 * @method self message(string $value)
 * @method self username(string $value)
 * @method self user_email(string $value)
 */
#[AllowDynamicProperties]
class AddTicket
{
    public ?string $staff_name;
    public string $subject;
    public string $message;
    public string $username;
    public string $user_email;

    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value):
            $this->$key = $value;
        endforeach;
    }

    /**
     * Build a ticket for a username.
     * @param string $username
     * @param string $subject
     * @param string $message
     * @param string|null $staff_name staff (admin) name shown as the sender
     * @return self
     */
    public static function by_username(string $username, string $subject, string $message, ?string $staff_name = null): self
    {
        return (new self(['username' => $username, 'subject' => $subject, 'message' => $message]))
            ->staff_name($staff_name);
    }

    /**
     * Build a ticket for a user email.
     * @param string $user_email
     * @param string $subject
     * @param string $message
     * @param string|null $staff_name staff (admin) name shown as the sender
     * @return self
     */
    public static function by_email(string $user_email, string $subject, string $message, ?string $staff_name = null): self
    {
        return (new self(['user_email' => $user_email, 'subject' => $subject, 'message' => $message]))
            ->staff_name($staff_name);
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
     * Build the ticket body, empty values are skipped.
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
