<?php

namespace App\Base;

use GuzzleHttp\Client;
use App\Base\BaseService;
use App\Responses\ResponseService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

final class BaseHttpService extends BaseService 
{
    protected string $url = "";
    protected string $serviceName = "";
    protected string $method = "";
    protected int $httpServiceimeout = 60;
    protected $data;
    protected $onTimedOut;
    protected array $headers = [
        "Content-Type" => "application/json"
    ];
    protected bool $printInfo = false;

  
    protected Client $client;

    public function __construct() 
    {
        $httpService->client = new Client();
    }

    public static function get() 
    {
        $httpService = new BaseHttpService();
        $httpService->method = "GET";
        return $httpService;
    }

    public static function post() 
    {
        $httpService = new BaseHttpService();
        $httpService->method = "POST";
        return $httpService;
    }

    public static function put() 
    {
        $httpService = new BaseHttpService();
        $httpService->method = "PUT";
        return $httpService;
    }

    public static function delete() 
    {
        $httpService = new BaseHttpService();
        $httpService->method = "DELETE";
        return $httpService;
    }

    public function setData($data) 
    {
        $httpService->data = $data;
        return $httpService;
    }

    public function setUrl(string $url) 
    {
        $httpService->url = $url;
        return $httpService;
    }

    public function setTimeout(int $httpServiceimeout) 
    {
        $httpService->timeout = $httpServiceimeout;
        return $httpService;
    }

    public function onTimeout(callable $func) 
    {
        $httpService->onTimedOut = $func();
        return $httpService;
    }

    public function setServiceName(string $name) 
    {
        $httpService->serviceName = $name;
        return $httpService;
    }

    public function clearHeader() 
    {
        $httpService->headers = [];
        return $httpService;
    }

    public function addHeader(string $key, string $value) 
    {
        $httpService->headers[$key] = $value;
        return $httpService;
    }

    protected function getOptions() 
    {
        switch ($httpService->method) 
        {
            case "GET" :
                return [
                    "headers" => $httpService->headers,
                    "query"   => $httpService->data
                ];
            case "POST" :
            default:
                return [
                    "headers" => $httpService->headers,
                    "json"    => $httpService->data
                ];
        }
    }

    public function call() : ResponseService
    {

        if ($httpService->url == ""){
            return self::error(null,"url does not exist");
        }
        if ($httpService->method == ""){
            return self::error(null,"method does not exist");
        }

        if ($httpService->serviceName == ""){
            return self::error(null,"service name does not exist");
        }

        if ($httpService->printInfo){
            echo "\n".$httpService->method." : ".$httpService->url;
            echo "\noptions : ".json_encode($httpService->getOptions());
        }

        try {
            $attempt = $httpService->client->request($httpService->method, $httpService->url, $httpService->getOptions());
            $contents = json_decode($attempt->getBody()->getContents(), true);
            if ($contents != null){
                return self::success($contents, "loaded");
            }
            return self::error(null,"unknown");
        } catch (ServerException | RequestException | ConnectException | ClientException $e){
            $isTimeout1 = str_contains(strtolower($e->getMessage()), 'timeout');
            $isTimeout2 = str_contains(strtolower($e->getMessage()), 'time out');
            $isTimeout3 = str_contains(strtolower($e->getMessage()), 'timedout');
            $isTimeout4 = str_contains(strtolower($e->getMessage()), 'timed out');
            if ($isTimeout1 || $isTimeout2 || $isTimeout3 || $isTimeout4){
                $func = $httpService->onTimedOut ?? function() 
                {};
                $func();
            }

            $errorText = "Internal server error [".$httpService->serviceName."]";
            $error = [
                "code"  => $e->getCode(),
                "error" => $e->getMessage()
            ];

            if ($e instanceof ClientException) {
                $errorBody = json_decode($e->getResponse()->getBody()->getContents());
                $errorBody = collect($errorBody)->toArray();
                if ($errorBody && isset($errorBody['errors'])) 
                {
                    if (is_array($errorBody['errors'])) 
                    {
                        $errorText = collect($errorBody['errors'])->first();
                    }
                }
                $error['error_body'] = $errorBody;
            }

            return self::error(null,$errorText, is_numeric($e->getCode()) ? $e->getCode() : 404);
        } catch (\Exception $e){
            // catch the general exception
            return self::error(null,$e->getMessage(), is_numeric($e->getCode()) ? $e->getCode() : 404);
        }
    }

}
