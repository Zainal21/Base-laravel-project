<?php

namespace App\Base;

use GuzzleHttp\Client;
use App\Base\BaseService;
use App\Responses\ResponseService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

final class BaseHttpService extends BaseService {

    protected string $url = "";
    protected string $serviceName = "";
    protected string $method = "";
    protected int $timeout = 60;
    protected $data;
    protected $onTimedOut;
    protected array $headers = [
        "Content-Type" => "application/json"
    ];
    protected bool $printInfo = false;

    protected Client $client;

    public function __construct() 
    {
        $this->client = new Client();
    }

    public static function get() 
    {
        $t = new BaseHttpService();
        $t->method = "GET";
        return $t;
    }

    public static function post() 
    {
        $t = new BaseHttpService();
        $t->method = "POST";
        return $t;
    }

    public static function put() 
    {
        $t = new BaseHttpService();
        $t->method = "PUT";
        return $t;
    }

    public static function delete() 
    {
        $t = new BaseHttpService();
        $t->method = "DELETE";
        return $t;
    }

    public function setData($data) 
    {
        $this->data = $data;
        return $this;
    }

    public function setUrl(string $url) 
    {
        $this->url = $url;
        return $this;
    }

    public function setTimeout(int $timeout) 
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function onTimeout(callable $func) 
    {
        $this->onTimedOut = $func();
        return $this;
    }

    public function setServiceName(string $name) 
    {
        $this->serviceName = $name;
        return $this;
    }

    public function clearHeader() 
    {
        $this->headers = [];
        return $this;
    }

    public function addHeader(string $key, string $value) 
    {
        $this->headers[$key] = $value;
        return $this;
    }

    protected function getOptions() 
    {
        switch ($this->method) 
        {
            case "GET" :
                return [
                    "headers" => $this->headers,
                    "query"   => $this->data
                ];
            case "POST" :
            default:
                return [
                    "headers" => $this->headers,
                    "json"    => $this->data
                ];
        }
    }
    
    public function call() : ResponseService
    {
        if ($this->url == ""){
            return self::error(null,"url does not exist");
        }
        if ($this->method == ""){
            return self::error(null,"method does not exist");
        }
        if ($this->serviceName == ""){
            return self::error(null,"service name does not exist");
        }
        if ($this->printInfo){
            echo "\n".$this->method." : ".$this->url;
            echo "\noptions : ".json_encode($this->getOptions());
        }

        try {
            $attempt = $this->client->request($this->method, $this->url, $this->getOptions());
            $contents = json_decode($attempt->getBody()->getContents(), true);
            if ($contents != null) 
            {
                return self::success($contents, "loaded");
            }
            return self::error(null,"unknown");
        } catch (ServerException | RequestException | ConnectException | ClientException $e){
            $isTimeout1 = str_contains(strtolower($e->getMessage()), 'timeout');
            $isTimeout2 = str_contains(strtolower($e->getMessage()), 'time out');
            $isTimeout3 = str_contains(strtolower($e->getMessage()), 'timedout');
            $isTimeout4 = str_contains(strtolower($e->getMessage()), 'timed out');
            if ($isTimeout1 || $isTimeout2 || $isTimeout3 || $isTimeout4){
                $func = $this->onTimedOut ?? function() 
                {};
                $func();
            }

            $errorText = "Internal server error [".$this->serviceName."]";
            $error = [
                "code"  => $e->getCode(),
                "error" => $e->getMessage()
            ];

            if ($e instanceof ClientException){
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
        } catch (\Exception $e) {
            return self::error(null,$e->getMessage(), is_numeric($e->getCode()) ? $e->getCode() : 404);
        }
    }

}
