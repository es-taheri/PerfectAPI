<?php

namespace PerfectAPI;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use PerfectAPI\Api\Result;
use Psr\Http\Message\ResponseInterface;

class Api
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
        if (isset($config['api_url'], $config['apikey'], $config['timeout'])) {
            extract($config);
            $this->apiurl = $api_url;
            $this->apikey = $apikey;
            $this->timeout = $timeout;
            $this->client = new Client([
                'base_uri' => $api_url,
                'headers' => ['Content-Type' => 'application/json'],
                'form_params' => ['key' => $apikey],
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
                'headers' => ['Content-Type' => 'application/json'],
                'form_params' => ['key' => $this->apikey],
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

    public function call($action, $params = []): Result
    {
        $params['action'] = $action;
        return $this->request('POST', '', $params);
    }

    public function concurrent(array $calls): array
    {
        $requests = [];
        foreach ($calls as $a_call):
            [$action, $params] = $a_call;
            $params['action'] = $action;
            $requests[] = ['POST', '', $params];
        endforeach;
        return $this->multi_request($requests);
    }

    protected function request($method, $endpoint, $params = []): Result
    {
        $url = rtrim($this->apiurl, '/') . '/' . ltrim($endpoint, '/');
        $options = [];
        if (strtoupper($method) === 'GET')
            $options['query'] = $params;
        else
            $options['form_params'] = $params;
        return $this->client
            ->requestAsync($method, $url, $options)
            ->then(
                function (ResponseInterface $result) {
                    $response = $result->getBody()->getContents();
                    return new Result(['ok' => true, 'response' => $response]);
                },
                function (RequestException $e) {
                    $resp = $e->getResponse();
                    return new Result([
                        'ok' => false,
                        'error' => $e->getMessage(),
                        @'response' => $resp?->getBody()->getContents()
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
                $options['form_params'] = $params;
            $promises[] = $this->client
                ->requestAsync($httpMethod, $url, $options)
                ->then(
                    function ($result) {
                        $response = $result->getBody()->getContents();
                        return ['ok' => true, 'response' => $response];
                    },
                    function (GuzzleException $e) {
                        $resp = $e->getResponse();
                        return [
                            'ok' => false,
                            'error' => $e->getMessage(),
                            @'response' => $resp ? $resp->getBody()->getContents() : null
                        ];
                    }
                );
        }
        return Utils::settle($promises)->wait();
    }

    public static function config($api_url, $apikey, $timeout = self::TIMEOUT): array
    {
        return ['api_url' => $api_url, 'apikey' => $apikey, 'timeout' => $timeout];
    }
}