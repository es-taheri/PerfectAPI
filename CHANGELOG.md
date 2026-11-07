# Changelog

## 2.0.0

Full sync with the **Perfect Panel Admin API v2** documentation and a cleanup of
the client API.

### Added — Admin API

- `PerfectAPI\AdminApi\Payments` — `GET /payments`, `POST /payments/add`
- `PerfectAPI\AdminApi\Filters\PaymentList` — payments list filters
- `PerfectAPI\AdminApi\Setters\AddPayment` — payment body (`by_id()`,
  `by_username()`, `by_email()`)
- `PerfectAPI\AdminApi\Ticket::list()`, `Ticket::get()`, `Ticket::reply()` —
  `GET /tickets`, `GET /tickets/{id}`, `POST /tickets/{id}/reply`
- `PerfectAPI\AdminApi\Filters\TicketList` — tickets list filters
- `PerfectAPI\AdminApi\Setters\AddTicket` — ticket body (`by_username()`,
  `by_email()`)
- `PerfectAPI\AdminApi\User::add()`, `User::edit()`, `User::get()` —
  `POST /users/add`, `POST /users/{id}/edit`, `GET /users?ids=`
- `PerfectAPI\AdminApi\Setters\AddUser`, `PerfectAPI\AdminApi\Setters\EditUser`
- `include` filter (`user_data`, `last_update`, `provider_response`) on the
  orders list
- `Result::status()`, `Result::result()`, `Response::error_code()`,
  `Response::count()`, `Response::items()`, `Response::raw()`
- `Response::data()/pagination()` accept `json` as the convert type and throw on
  an unknown type instead of an uncatchable `UnhandledMatchError`
- configurable ssl verification (`config(..., bool $verify = true)`)
- status / mode / sort / include constants on the `Filters` classes
- local validation of the documented required fields before sending a request

### Fixed — Admin API

- `Order::cancel_refund()` sent `reason`, the api expects `cancel_reason`
- `Order::pull()` and `Cancel::pull()`/`Refill::pull()` no longer send an empty
  `service_ids` (api answered *Invalid service ID*), and the default/limit is
  clamped to the documented maximum of 100
- `Order::update()` and `Refill::change_status()` sent every order field, now
  empty fields are skipped, `id` is required and the 100 items cap is enforced
- `Setters\RejectCancel::remove()` removed elements by value-as-key
- `Filters\*List::out()` no longer sends unset/empty filters
- `OrderList` was missing the `include` parameter and `UserList` was missing
  `#[AllowDynamicProperties]`
- `AdminApi::concurrent()` returned raw `Utils::settle()` arrays with one array
  and one `Result` shape; it now always returns keyed `Result` objects
- `AdminApi::timeout()` returned the stale timeout after being set
- `AdminApi::ping()` always dialled port 443 even for `http` urls
- `Result::error()`/`Response::error()` could throw a `TypeError` on a missing
  key / non-string message
- TLS verification was hard disabled, it is now enabled by default and can be
  turned off for self-signed panels

### Added / fixed — Client API

- `Api\Order::status()`/`cancel()` and `Api\Refill::status()` resolved the wrong
  class names (`PerfectAPI\Api\Api\Filters\...`)
- `Api\Request` sent `form_params` with an `application/json` content type
- `Api::timeout()`, `Api::ping()`, `Api::concurrent()` — same fixes as the admin
  api
- `Api\Response` no longer crashes on an empty/missing `error` field, and
  exposes `get($key, $type)`
- `Api\Service::get()` returns a failed `Result` instead of a broken response
  when the service does not exist
- implicit nullable parameters (`int $x = null`) replaced with `?int` for PHP
  8.4+

### Compatibility

- `Ticket::add($staff, $subject, $message, $username_or_email)` keeps working
  next to the new `Ticket::add(AddTicket|array $ticket)`
- `concurrent()` now returns `Result[]` keyed like the request array
- requires PHP 8.1+
