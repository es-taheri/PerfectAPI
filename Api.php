<?php

namespace PerfectAPI;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use JSON\json;
use PerfectAPI\Api\Result;
use Psr\Http\Message\ResponseInterface;

/**
 * Perfect Panel client API v2 client.
 *
 * Every user endpoint is reachable through `call()`/`concurrent()`, and the
 * typed helpers live in the `PerfectAPI\Api\*` classes (Order, Service, Refill).
 *
 * @link https://my.panel.com/api/v2
 */
class Api
{
    public const TIMEOUT = 10;

    protected string $apiurl;

    protected string $apikey;

    protected int $timeout;

    protected bool $verify;

    protected Client $client;

    /**
     * @param array $config see self::config()
     * @throws Exception
     */
    public function __construct(array $config)
    {
        if (empty($config['api_url']) || empty($config['apikey']))
            throw new Exception('Invalid api configuration, api_url and apikey are required');
        $this->apiurl = (string)$config['api_url'];
        $this->apikey = (string)$config['apikey'];
        $this->timeout = (int)($config['timeout'] ?? self::TIMEOUT);
        $this->verify = (bool)($config['verify'] ?? true);
        $this->client = $this->make_client($this->timeout);
    }

    /**
     * Build a configuration array for the client.
     * @param string $api_url panel api url e.g. `https://my.panel.com/api/v2`
     * @param string $apikey panel api key
     * @param int $timeout request timeout in seconds
     * @param bool $verify verify the peer's ssl certificate (disable only for self-signed panels)
     * @return array
     */
    public static function config(string $api_url, string $apikey, int $timeout = self::TIMEOUT, bool $verify = true): array
    {
        return ['api_url' => $api_url, 'apikey' => $apikey, 'timeout' => $timeout, 'verify' => $verify];
    }

    /**
     * Get or set the request timeout.
     * @param int|null $value new timeout in seconds, don't set it to just read the current value
     * @return int current timeout in seconds
     */
    public function timeout(?int $value = null): int
    {
        if (!empty($value)) {
            $this->timeout = $value;
            $this->client = $this->make_client($this->timeout);
        }
        return $this->timeout;
    }

    /**
     * Measure the round trip time of a raw tcp connection to the panel.
     * @return float|false round trip time in milliseconds or false on failure
     */
    public function ping(): float|false
    {
        $host = parse_url($this->apiurl, PHP_URL_HOST);
        if (empty($host))
            return false;
        $scheme = parse_url($this->apiurl, PHP_URL_SCHEME);
        $port = parse_url($this->apiurl, PHP_URL_PORT);
        if (empty($port))
            $port = ($scheme === 'http') ? 80 : 443;
        $st = microtime(true);
        $connection = @fsockopen($host, (int)$port, $error_code, $error, $this->timeout);
        $et = microtime(true);
        $ms = round(($et - $st) * 1000);
        if ($connection) {
            fclose($connection);
            return $ms;
        } else
            return false;
    }

    /**
     * Call any api action.
     * @param string $action api action e.g. `add`, `status`, `refill`
     * @param array $params api parameters
     * @return Result
     */
    public function call(string $action, array $params = []): Result
    {
        $params['action'] = $action;
        return $this->request('POST', '', $params);
    }

    /**
     * Run multiple api actions concurrently.
     * @param array $calls array of `[$action, $params]` items
     * @return Result[] results keyed exactly like the given `$calls`
     */
    public function concurrent(array $calls): array
    {
        $requests = [];
        foreach ($calls as $key => $a_call):
            [$action, $params] = array_pad((array)$a_call, 2, []);
            $params = (array)$params;
            $params['action'] = $action;
            $requests[$key] = ['POST', '', $params];
        endforeach;
        return $this->multi_request($requests);
    }

    /**
     * Send a single request.
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @return Result
     */
    protected function request(string $method, string $endpoint = '', array $params = []): Result
    {
        $method = strtoupper($method);
        return $this->client
            ->requestAsync($method, $this->url($endpoint), $this->options($method, $params, true))
            ->then(
                function (ResponseInterface $response): Result {
                    return $this->make_result($response);
                },
                function (GuzzleException $e): Result {
                    return $this->make_error($e);
                }
            )->wait();
    }

    /**
     * Send multiple requests concurrently.
     * @param array $requests array of `[$method, $path, $params]` items
     * @return Result[]
     */
    protected function multi_request(array $requests): array
    {
        $promises = [];
        foreach ($requests as $key => $req):
            [$method, $endpoint, $params] = array_pad((array)$req, 3, []);
            $method = strtoupper((string)$method);
            $promises[$key] = $this->client
                ->requestAsync($method, $this->url((string)$endpoint), $this->options($method, (array)$params, true))
                ->then(
                    function (ResponseInterface $response): Result {
                        return $this->make_result($response);
                    },
                    function (GuzzleException $e): Result {
                        return $this->make_error($e);
                    }
                );
        endforeach;
        $results = [];
        foreach (Utils::settle($promises)->wait() as $key => $settled):
            $results[$key] = ($settled['state'] === 'fulfilled')
                ? $settled['value']
                : Result::make_fail($this->exception_message($settled['reason']));
        endforeach;
        return $results;
    }

    /**
     * Build the absolute request url.
     * @param string $endpoint
     * @return string
     */
    protected function url(string $endpoint = ''): string
    {
        return rtrim(rtrim($this->apiurl, '/') . '/' . ltrim($endpoint, '/'), '/');
    }

    /**
     * Build guzzle request options for the given method.
     * @param string $method
     * @param array $params
     * @param bool $with_key add the api key to the request parameters
     * @return array
     */
    protected function options(string $method, array $params, bool $with_key = true): array
    {
        if ($with_key)
            $params['key'] = $this->apikey;
        $params = array_filter($params, static function ($value): bool {
            return !($value === null || $value === '' || $value === []);
        });
        if ($method === 'GET')
            return ['query' => $params];
        return ['form_params' => $params];
    }

    /**
     * Convert a successful http response to a Result.
     * @param ResponseInterface $response
     * @return Result
     */
    protected function make_result(ResponseInterface $response): Result
    {
        $body = (string)$response->getBody();
        return new Result([
            'ok' => json::_is($body),
            'status' => $response->getStatusCode(),
            'response' => $body
        ]);
    }

    /**
     * Convert a failed http request to a Result.
     * @param GuzzleException $e
     * @return Result
     */
    protected function make_error(GuzzleException $e): Result
    {
        $status = null;
        $body = null;
        if ($e instanceof RequestException && $e->hasResponse()) {
            $status = $e->getResponse()->getStatusCode();
            $body = (string)$e->getResponse()->getBody();
        }
        return new Result([
            'ok' => false,
            'status' => $status,
            'error' => $e->getMessage(),
            'response' => $body
        ]);
    }

    /**
     * @param mixed $reason
     * @return string
     */
    private function exception_message(mixed $reason): string
    {
        if ($reason instanceof Exception)
            return $reason->getMessage();
        return is_scalar($reason) ? (string)$reason : 'Unknown error';
    }

    /**
     * Set up the guzzle client.
     * @param int $timeout
     * @return Client
     */
    private function make_client(int $timeout): Client
    {
        return new Client([
            'base_uri' => rtrim($this->apiurl, '/') . '/',
            'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/x-www-form-urlencoded'],
            'timeout' => $timeout,
            'verify' => $this->verify
        ]);
    }

    /**
     * Make sure the required parameters are present before sending a request.
     * @param array $params
     * @param array $required list of required parameter names
     * @return void
     * @throws Exception
     */
    protected static function check(array $params, array $required): void
    {
        foreach ($required as $field):
            if (!isset($params[$field]) || $params[$field] === '')
                throw new Exception("Missing required '$field' parameter");
        endforeach;
    }
}
