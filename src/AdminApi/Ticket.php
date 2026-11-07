<?php

namespace PerfectAPI\AdminApi;

use Exception;
use PerfectAPI\AdminApi;
use PerfectAPI\AdminApi\Filters\TicketList;
use PerfectAPI\AdminApi\Setters\AddTicket;

/**
 * Tickets endpoints of the admin api.
 */
class Ticket extends AdminApi
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Search and get a list of tickets.
     * @param TicketList $filters
     * @return Result
     */
    public function list(TicketList $filters): Result
    {
        return $this->request('GET', 'tickets', $filters->out());
    }

    /**
     * Get a ticket with its messages.
     * @param int $id ticket id
     * @return Result
     */
    public function get(int $id): Result
    {
        return $this->request('GET', "tickets/$id");
    }

    /**
     * Add a new ticket for a panel user.
     *
     * Accepts an `AddTicket` object (or a raw array), or the positional
     * `add($staff_name, $subject, $message, $username_or_email)` form.
     *
     * @param AddTicket|array|string $ticket ticket body, or the staff name
     * @param string|null $subject
     * @param string|null $message
     * @param string|null $username_or_email the ticket user, an `@` makes it an email
     * @return Result
     * @throws Exception
     */
    public function add(AddTicket|array|string $ticket, ?string $subject = null, ?string $message = null, ?string $username_or_email = null): Result
    {
        if (is_string($ticket)):
            $params = ['staff_name' => $ticket, 'subject' => (string)$subject, 'message' => (string)$message];
            $identity = (string)$username_or_email;
            $params[str_contains($identity, '@') ? 'user_email' : 'username'] = $identity;
        else:
            $params = is_array($ticket) ? $ticket : $ticket->out();
        endif;
        self::check($params, ['subject', 'message']);
        self::check_one($params, ['username', 'user_email']);
        return $this->request('POST', 'tickets/add', $params);
    }

    /**
     * Reply to a ticket on behalf of a staff member.
     * @param int $id ticket id
     * @param string $staff_name staff (admin) name shown as the sender
     * @param string $message reply text
     * @return Result
     * @throws Exception
     */
    public function reply(int $id, string $staff_name, string $message): Result
    {
        $params = ['staff_name' => $staff_name, 'message' => $message];
        self::check($params, ['staff_name', 'message']);
        return $this->request('POST', "tickets/$id/reply", $params);
    }
}
