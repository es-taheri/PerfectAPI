# PerfectAPI

A small, dependency-light PHP client for **Perfect Panel** websites.

It talks to both APIs that every Perfect Panel installation exposes:

| API | Base URL | Used by | Class |
| --- | --- | --- | --- |
| Client API v2 | `https://your-panel.com/api/v2` | panel **users** (place/cancel orders, refill, services) | `PerfectAPI\Api` |
| Admin API v2 | `https://your-panel.com/adminapi/v2` | panel **owners / staff** (orders, users, payments, tickets, cancel & refill tasks) | `PerfectAPI\AdminApi` |

Both APIs are covered 1:1 with the official documentation, with typed filters,
typed request bodies and a uniform result/response layer.

[![Latest Version](https://img.shields.io/packagist/v/estaheri/perfectapi.svg?style=flat-square)](https://packagist.org/packages/estaheri/perfectapi)
[![License](https://img.shields.io/packagist/l/estaheri/perfectapi.svg?style=flat-square)](LICENSE)
[![PHP](https://img.shields.io/badge/php-%3E%3D8.1-777bb4.svg?style=flat-square)](https://www.php.net)

## Requirements

- PHP **8.1+**
- [Composer](https://getcomposer.org)
- A Perfect Panel api key (client key from your account, or an admin key created
  in the panel admin area)

## Installation

```bash
composer require estaheri/perfectapi
```

## Configuration

```php
use PerfectAPI\AdminApi;
use PerfectAPI\Api;

// Admin API (panel owner)
$admin_config = AdminApi::config(
    'https://my.panel.com/adminapi/v2', // base url, no trailing slash needed
    'your-admin-api-key'
);

// Client API (panel user)
$client_config = Api::config(
    'https://my.panel.com/api/v2',
    'your-client-api-key'
);
```

`config()` accepts two extra optional arguments:

| Argument | Default | Description |
| --- | --- | --- |
| `$timeout` | `10` | request timeout in seconds |
| `$verify` | `true` | verify the panel ssl certificate — only disable it for panels with a self-signed certificate |

Every resource class (`Order`, `User`, `Payments`, `Ticket`, `Cancel`, `Refill`)
is instantiated with the same config array:

```php
use PerfectAPI\AdminApi\Order;
use PerfectAPI\AdminApi\User;

$orders = new Order($admin_config);
$users  = new User($admin_config);
```

You can change the timeout at runtime, and measure the panel latency:

```php
$orders->timeout(30);   // set + return the new timeout
$orders->timeout();     // read the current timeout

$orders->ping();        // float (ms) or false
```

## Reading a result

Every call returns a `Result` (`PerfectAPI\AdminApi\Result` or
`PerfectAPI\Api\Result`), so a failing transport never throws:

```php
$result = $orders->list($filter);

$result->ok();      // bool   — request was sent and answered with valid json
$result->status();  // ?int   — http status code
$result->error();   // ?string — curl/connection/timeout error message
$result->response();// ?Response — parsed api response envelope
$result->response(true); // ?string — raw response body
```

`Response` wraps the api envelope:

```php
$response = $result->response();

$response->success();    // bool    — the api accepted the request
$response->error();      // ?string — api error message
$response->error_code(); // ?int    — api error code

$response->data();       // object|array|string|null  ('object' by default)
$response->data('array');// same data as an array
$response->data('json'); // same data as a json string
$response->pagination(); // paginated endpoints only
$response->count();      // data.count of a list response
$response->items();      // data.list of a list response (array)
$response->raw();        // the whole envelope
```

So the usual flow is:

```php
$result = $orders->list($filter);

if (!$result->ok())
    die('network error: ' . $result->error());

if (!$result->response()->success())
    die('api error: ' . $result->response()->error());

foreach ($result->response()->items() as $order) {
    echo $order->id . ' — ' . $order->status . PHP_EOL;
}
```

## Admin API

### Orders

| Method | Endpoint |
| --- | --- |
| `pull(?array $service_ids = null, int $limit = 100)` | `POST /orders/pull` |
| `list(OrderList $filters)` | `GET /orders` |
| `order(int $id)` / `get(int $id)` | `GET /orders/{id}` |
| `update(array $orders)` | `POST /orders/update` |
| `edit_link(int $id, string $link)` | `POST /orders/{id}/edit-link` |
| `resend(int\|array $ids)` | `POST /orders/resend` |
| `change_status(int\|array $ids, string $status)` | `POST /orders/change-status` |
| `partial(int $id, int $remains)` | `POST /orders/{id}/set-partial` |
| `cancel(int\|array $ids)` — creates a cancel task | `POST /orders/request-cancel` |
| `cancel_refund(int\|array $ids, ?string $reason = null)` | `POST /orders/cancel` |

List orders:

```php
use PerfectAPI\AdminApi\Filters;

$filter = Filters::Order()
    ->order_status(Filters::STATUS_Processing)
    ->mode(Filters::MODE_Manual)
    ->created_from(strtotime('-7 days'))
    ->include([Filters::INCLUDE_UserData, Filters::INCLUDE_LastUpdate])
    ->sort(Filters::ORDER_SORT_DateDesc)
    ->limit(100)
    ->offset(0);

$result = (new Order($admin_config))->list($filter);
```

`Filters::Order()` matches the api query parameters: `ids`, `external_order_ids`,
`created_from`, `created_to`, `order_status`, `mode`, `service_ids`,
`creation_type`, `user`, `provider`, `ip_address`, `link`, `offset`, `limit`,
`sort` and `include`. Empty filters are never sent. Any other filter can be
appended with `->add('key', $value)`.

Update one or multiple orders (status, start count, remains, cancel reason):

```php
use PerfectAPI\AdminApi\Setters;

$result = $orders->update([
    Setters::Order()->id(12)->status(Filters::STATUS_Processing)->start_count(1200),
    Setters::Order()->id(15)->status(Filters::STATUS_Partial)->remains(500),
    Setters::Order()->id(18)->status(Filters::STATUS_Canceled)->cancel_reason('Incorrect link'),
]);
```

Pull manual orders and move them to processing:

```php
$result = $orders->pull([1061, 1062], 50);
foreach ($result->response()->items() as $order) {
    // process the manual order ...
}
```

### Users

| Method | Endpoint |
| --- | --- |
| `list(UserList $filters)` | `GET /users` |
| `get(int $id)` | `GET /users?ids={id}` |
| `add(AddUser\|array $user)` | `POST /users/add` |
| `edit(int $id, EditUser\|array $user)` | `POST /users/{id}/edit` |

```php
use PerfectAPI\AdminApi\Filters;
use PerfectAPI\AdminApi\Setters;
use PerfectAPI\AdminApi\User;

$users = new User($admin_config);

$filter = Filters::User()
    ->status(Filters::USER_Active)
    ->limit(50);

$list = $users->list($filter);

$created = $users->add(
    Setters::User()
        ->username('john')
        ->email('john@mail.com')
        ->password('12qw34er5ty')
        ->first_name('John')
        ->allowed_methods(Setters\AddUser::METHODS_All) // 'all', 'none', 'new_users' or 'custom'
);

// allow only payment methods 50 and 51
$created = $users->add(
    Setters::User()
        ->username('jane')
        ->email('jane@mail.com')
        ->password('12qw34er5ty')
        ->custom([50, 51])
);
```

Edit a user — unset fields are not sent, `null` clears a stored value:

```php
$users->edit(345, Setters::EditUser()
    ->email('new@mail.com')
    ->phone(null)                 // clear the phone
    ->allow_methods([50, 51])
    ->disallow_methods([55])
    ->hidden_services(Setters\EditUser::HIDDEN_Allowed)); // 'allowed' | 'not_allowed'
```

### Payments

| Method | Endpoint |
| --- | --- |
| `list(PaymentList $filters)` | `GET /payments` |
| `add(AddPayment\|array $payment)` | `POST /payments/add` |

```php
use PerfectAPI\AdminApi\Filters;
use PerfectAPI\AdminApi\Payments;
use PerfectAPI\AdminApi\Setters\AddPayment;

$payments = new Payments($admin_config);

$list = $payments->list(
    Filters::Payment()
        ->payment_status(Filters::PAYMENT_Completed)
        ->sort(Filters::PAYMENT_SORT_CreatedAtDesc)
        ->limit(100)
);

// add a payment by user id, username or email
$payments->add(AddPayment::by_id(42, 12, 'Perfect Money USD', 'added via admin api'));
$payments->add(AddPayment::by_username('john', 12, 'Perfect Money USD'));
$payments->add(AddPayment::by_email('john@mail.com', 12.5, 'Perfect Money USD', 'bonus', true));
```

### Tickets

| Method | Endpoint |
| --- | --- |
| `list(TicketList $filters)` | `GET /tickets` |
| `get(int $id)` | `GET /tickets/{id}` |
| `add(AddTicket\|array $ticket)` | `POST /tickets/add` |
| `reply(int $id, string $staff_name, string $message)` | `POST /tickets/{id}/reply` |

```php
use PerfectAPI\AdminApi\Filters;
use PerfectAPI\AdminApi\Setters\AddTicket;
use PerfectAPI\AdminApi\Ticket;

$tickets = new Ticket($admin_config);

$list = $tickets->list(
    Filters::Ticket()
        ->status(Filters::TICKET_Pending)
        ->is_read(false)
        ->limit(50)
);

$ticket = $tickets->get(255);

$tickets->add(AddTicket::by_username('john', 'Order problem', 'My order is stuck.', 'admin'));
$tickets->add(AddTicket::by_email('john@mail.com', 'Order problem', 'My order is stuck.'));

$tickets->reply(255, 'admin', 'We are checking your order.');
```

### Cancel tasks

| Method | Endpoint |
| --- | --- |
| `pull(?array $service_ids = null, int $limit = 100)` | `POST /cancel/pull` |
| `reject(RejectCancel\|int\|array $ids)` | `POST /cancel/reject` |

```php
use PerfectAPI\AdminApi\Cancel;
use PerfectAPI\AdminApi\Setters;
use PerfectAPI\AdminApi\Setters\RejectCancel;

$cancel = new Cancel($admin_config);

$pulled = $cancel->pull([84], 100);

$cancel->reject(Setters::RejectCancel([12, 33, 46]));
// or
$cancel->reject([12, 33, 46]);
```

### Refill tasks

| Method | Endpoint |
| --- | --- |
| `pull(?array $service_ids = null, int $limit = 100)` | `POST /refill/pull` |
| `change_status(array $refill_tasks)` | `POST /refill/change-status` |

```php
use PerfectAPI\AdminApi\Refill;
use PerfectAPI\AdminApi\Setters;
use PerfectAPI\AdminApi\Setters\RefillTask;

$refill = new Refill($admin_config);

$pulled = $refill->pull(null, 100);

$refill->change_status([
    Setters::RefillTask()->id(345)->status(RefillTask::STATUS_Completed),
    Setters::RefillTask()->id(346)->status(RefillTask::STATUS_Rejected),
]);
```

### Raw calls & concurrency

Any endpoint (including ones added to the panel later) is reachable directly:

```php
$result = $orders->call('POST', 'orders/pull', ['limit' => 100]);
$result = $orders->call('GET', 'orders', ['ids' => '12,33,46']);
```

Multiple admin calls at once (keep in mind the **5 requests per second** limit
of the admin api, otherwise the panel answers `429 Too Many Requests`):

```php
$results = $orders->concurrent([
    ['GET', 'orders', ['limit' => 10]],
    ['GET', 'users', ['limit' => 10]],
    ['GET', 'payments', ['limit' => 10]],
]);

foreach ($results as $key => $result) {
    // $key is exactly the same key used in the $calls array
}
```

## Client API

The client api is action based, every request is a `POST` to `/api/v2` with an
`action` parameter.

### Orders

```php
use PerfectAPI\Api\Filters;
use PerfectAPI\Api\Order;
use PerfectAPI\Api\Setters\AddOrder;

$order = new Order($client_config);

// add
$result = $order->add(AddOrder::default(1061, 'https://instagram.com/p/xxx', 1000));

// drip feed
$result = $order->add(AddOrder::default(1061, 'https://instagram.com/p/xxx', 100, 5, 30));

// custom comments
$result = $order->add(AddOrder::custom_comments(10, 'https://instagram.com/p/xxx', ['nice', 'great']));

// subscription
$result = $order->add(AddOrder::subscription(20, 'username', 10, 20, 5));

// status of one or multiple orders
$status = $order->status(Filters::Status(1234));
$status = $order->status(Filters::Status([1234, 5678]));

// cancel
$order->cancel(Filters::Cancel(1234));
$order->cancel(Filters::Cancel([1234, 5678]));
```

### Services

```php
use PerfectAPI\Api\Service;

$service = new Service($client_config);

$all = $service->list();
$one = $service->get(1061);
$many = $service->get([1061, 1062]);
```

### Refills

```php
use PerfectAPI\Api\Filters;
use PerfectAPI\Api\Refill;

$refill = new Refill($client_config);

$refill->add(Filters::Refill(1234));
$refill->add([1234, 5678]);

$refill->status(Filters::Refill([111, 222]));
```

### Raw calls

```php
$result = $order->call('add', [
    'service' => 1061,
    'link' => 'https://instagram.com/p/xxx',
    'quantity' => 1000,
]);

$results = $order->concurrent([
    ['status', ['order' => 1234]],
    ['status', ['order' => 5678]],
]);
```

## Constants

`PerfectAPI\AdminApi\Filters`:

| Group | Constants |
| --- | --- |
| order status | `STATUS_Pending`, `STATUS_InProgress`, `STATUS_Processing`, `STATUS_Completed`, `STATUS_Partial`, `STATUS_Canceled`, `STATUS_Rejected`, `STATUS_Fail`, `STATUS_Error` |
| order mode | `MODE_Manual`, `MODE_Auto` |
| creation type | `CREATION_NewOrderForm`, `CREATION_Api`, `CREATION_MassOrderForm`, `CREATION_Subscription`, `CREATION_DripFeed`, `CREATION_Refiller` |
| order sort | `ORDER_SORT_DateDesc`, `ORDER_SORT_DateAsc` |
| order include | `INCLUDE_UserData`, `INCLUDE_LastUpdate`, `INCLUDE_ProviderResponse` |
| payment status | `PAYMENT_Waiting`, `PAYMENT_Completed`, `PAYMENT_Pending`, `PAYMENT_Fail`, `PAYMENT_Expired`, `PAYMENT_Hold`, `PAYMENT_Underpaid` |
| payment sort | `PAYMENT_SORT_CreatedAtDesc`, `PAYMENT_SORT_CreatedAtAsc`, `PAYMENT_SORT_UpdatedAtDesc`, `PAYMENT_SORT_UpdatedAtAsc` |
| user status | `USER_Active`, `USER_Suspended`, `USER_Unconfirmed` |
| user sort | `USER_SORT_CreatedAtDesc`, `USER_SORT_CreatedAtAsc` |
| ticket status | `TICKET_Pending`, `TICKET_Answered`, `TICKET_Closed`, `TICKET_Locked`, `TICKET_Welcome` |

`PerfectAPI\Api\Filters`: `STATUS_Pending`, `STATUS_Active`, `STATUS_InProgress`,
`STATUS_Processing`, `STATUS_Completed`, `STATUS_Partial`, `STATUS_Cancelled`,
`STATUS_Awaiting`, `STATUS_Error`, `STATUS_Fail`, `STATUS_Rejected`.

## Error handling

```php
try {
    $result = $orders->update([$update]);
} catch (Exception $e) {
    // invalid local input (missing id, too many items, ...)
}

if (!$result->ok()) {
    // transport problem: timeout, dns, tls, non-json body
    echo $result->error();
} elseif (!$result->response()->success()) {
    // api problem: validation error, missing access, not found
    echo $result->response()->error() . ' (' . $result->response()->error_code() . ')';
} else {
    $data = $result->response()->data();
}
```

Local validation is done before sending a request whenever the api documents a
required field, so you get an immediate `Exception` instead of a round trip.

## Example

See [`test.php`](test.php) for a runnable example:

```bash
PERFECT_PANEL_URL="https://my.panel.com/adminapi/v2" \
PERFECT_PANEL_ADMIN_KEY="your-key" \
php test.php
```

## License

[MIT](LICENSE) © Esmaeil Taheri
