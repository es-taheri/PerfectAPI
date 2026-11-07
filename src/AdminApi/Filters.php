<?php

namespace PerfectAPI\AdminApi;

use PerfectAPI\AdminApi\Filters\OrderList;
use PerfectAPI\AdminApi\Filters\PaymentList;
use PerfectAPI\AdminApi\Filters\TicketList;
use PerfectAPI\AdminApi\Filters\UserList;

/**
 * Factories for the admin api filters and the comma separated value helper.
 */
class Filters
{
    public const STATUS_Pending = 'pending';
    public const STATUS_InProgress = 'in_progress';
    public const STATUS_Processing = 'processing';
    public const STATUS_Completed = 'completed';
    public const STATUS_Partial = 'partial';
    public const STATUS_Canceled = 'canceled';
    public const STATUS_Rejected = 'rejected';
    public const STATUS_Fail = 'fail';
    public const STATUS_Error = 'error';

    public const MODE_Manual = 'manual';
    public const MODE_Auto = 'auto';

    public const CREATION_NewOrderForm = 'new_order_form';
    public const CREATION_Api = 'api';
    public const CREATION_MassOrderForm = 'mass_order_form';
    public const CREATION_Subscription = 'subscription';
    public const CREATION_DripFeed = 'drip_feed';
    public const CREATION_Refiller = 'refiller';

    public const ORDER_SORT_DateDesc = 'date-desc';
    public const ORDER_SORT_DateAsc = 'date-asc';

    public const PAYMENT_Waiting = 'waiting';
    public const PAYMENT_Completed = 'completed';
    public const PAYMENT_Pending = 'pending';
    public const PAYMENT_Fail = 'fail';
    public const PAYMENT_Expired = 'expired';
    public const PAYMENT_Hold = 'hold';
    public const PAYMENT_Underpaid = 'underpaid';

    public const PAYMENT_SORT_CreatedAtDesc = 'created-at-desc';
    public const PAYMENT_SORT_CreatedAtAsc = 'created-at-asc';
    public const PAYMENT_SORT_UpdatedAtDesc = 'updated-at-desc';
    public const PAYMENT_SORT_UpdatedAtAsc = 'updated-at-asc';

    public const USER_Active = 'active';
    public const USER_Suspended = 'suspended';
    public const USER_Unconfirmed = 'unconfirmed';

    public const USER_SORT_CreatedAtDesc = 'created-at-desc';
    public const USER_SORT_CreatedAtAsc = 'created-at-asc';

    public const TICKET_Pending = 'pending';
    public const TICKET_Answered = 'answered';
    public const TICKET_Closed = 'closed';
    public const TICKET_Locked = 'locked';
    public const TICKET_Welcome = 'welcome';

    public const INCLUDE_UserData = 'user_data';
    public const INCLUDE_LastUpdate = 'last_update';
    public const INCLUDE_ProviderResponse = 'provider_response';

    public static function Order(): OrderList
    {
        return new OrderList();
    }

    public static function User(): UserList
    {
        return new UserList();
    }

    public static function Payment(): PaymentList
    {
        return new PaymentList();
    }

    public static function Ticket(): TicketList
    {
        return new TicketList();
    }

    /**
     * Convert an id/value or an array of them to the api's comma separated format.
     * @param mixed $value
     * @return string
     */
    public static function to_comma(mixed $value): string
    {
        if (is_array($value))
            return implode(',', $value);
        return is_null($value) ? '' : (string)$value;
    }

    /**
     * Convert a filter value to a query string value (booleans become `1`/`0`).
     * @param mixed $value
     * @return string
     */
    public static function to_query(mixed $value): string
    {
        if (is_bool($value))
            return $value ? '1' : '0';
        return self::to_comma($value);
    }

    /**
     * Is this filter value empty (and so it should not be sent to the api)?
     * @param mixed $value
     * @return bool
     */
    public static function is_empty(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }
}
