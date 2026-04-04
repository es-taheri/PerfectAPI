<?php

namespace PerfectAPI;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use JSON\json;
use PerfectAPI\AdminApi\Result;
use Psr\Http\Message\ResponseInterface;

class AdminApi
{
    private string $apiurl;
    private string $apikey;
    private Client $client;
    const TIMEOUT = 10;
    private int $timeout;

    /**
     * @throws Exception
     */
    public function __construct(array $config)
    {
        if (isset($config['base_url'], $config['apikey'], $config['timeout'])) {
            extract($config);
            $this->apiurl = $base_url;
            $this->apikey = $apikey;
            $this->timeout = $timeout;
            $this->client = new Client([
                'base_uri' => $base_url,
                'headers' => ['Content-Type' => 'application/json', 'X-Api-Key' => $apikey],
                'timeout' => $timeout,
                'verify' => false
            ]);
        } else {
            throw new Exception('Invalid api configuration');
        }
    }

    public function timeout($value = null): ?int
    {
        if (empty($value))
            return $this->timeout;
        else
            $this->client = new Client([
                'base_uri' => $this->apiurl,
                'headers' => ['Content-Type' => 'application/json', 'X-Api-Key' => $this->apikey],
                'timeout' => $value,
                'verify' => false
            ]);
        return null;
    }

    public function ping(): float|false
    {
        $st = microtime(true);
        $connection = @fsockopen(parse_url($this->apiurl, PHP_URL_HOST), 443, $error_code, $error, $this->timeout);
        $et = microtime(true);
        $ms = round(($et - $st) * 1000);
        if ($connection) {
            fclose($connection);
            return $ms;
        } else
            return false;
    }

    protected function request($method, $endpoint, $params = []): Result
    {
        $url = rtrim($this->apiurl, '/') . '/' . ltrim($endpoint, '/');
        $options = [];
        if (strtoupper($method) === 'GET')
            $options['query'] = $params;
        else
            $options['body'] = json::_out($params);
        return $this->client
            ->requestAsync($method, $url, $options)
            ->then(
                function (ResponseInterface $result) {
                    $response = $result->getBody()->getContents();
                    return new Result(['ok' => json::_is($response), 'response' => $response]);
                },
                function (ClientException|ServerException $e) {
                    $resp = $e->getResponse();
                    return new Result([
                        'ok' => false,
                        'error' => $e->getMessage(),
                        @'response' => is_null($resp) ? null : $resp->getBody()->getContents()
                    ]);
                },
                function (ConnectException $e) {
                    return new Result([
                        'ok' => false,
                        'error' => $e->getMessage(),
                        @'response' => null
                    ]);
                },
                function (RequestException|GuzzleException $e) {
                    $resp = $e->getResponse();
                    return new Result([
                        'ok' => false,
                        'error' => $e->getMessage(),
                        @'response' => is_null($resp) ? null : $resp->getBody()->getContents()
                    ]);
                }
            )->wait();
    }

    protected function multi_request(array $requests): array
    {
        $promises = [];
        foreach ($requests as $req) {
            [$httpMethod, $endpoint, $params] = $req;
            $url = rtrim($this->apiurl, '/') . '/' . ltrim($endpoint, '/');
            $options = [];
            if (strtoupper($httpMethod) === 'GET')
                $options['query'] = $params;
            else
                $options['body'] = json::_out($params);
            $promises[] = $this->client
                ->requestAsync($httpMethod, $url, $options)
                ->then(
                    function ($result) {
                        $response = $result->getBody()->getContents();
                        return ['ok' => json::_is($response), 'response' => $response];
                    },
                    function (ClientException|ServerException $e) {
                        $resp = $e->getResponse();
                        return new Result([
                            'ok' => false,
                            'error' => $e->getMessage(),
                            @'response' => is_null($resp) ? null : $resp->getBody()->getContents()
                        ]);
                    },
                    function (ConnectException $e) {
                        return new Result([
                            'ok' => false,
                            'error' => $e->getMessage(),
                            @'response' => null
                        ]);
                    },
                    function (RequestException|GuzzleException $e) {
                        $resp = $e->getResponse();
                        return new Result([
                            'ok' => false,
                            'error' => $e->getMessage(),
                            @'response' => is_null($resp) ? null : $resp->getBody()->getContents()
                        ]);
                    }
                );
        }
        return Utils::settle($promises)->wait();
    }

    public static function config($base_url, $apikey, $timeout = self::TIMEOUT): array
    {
        return ['base_url' => $base_url, 'apikey' => $apikey, 'timeout' => $timeout];
    }
}
