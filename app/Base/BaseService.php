<?php

namespace App\Base;

use App\Contracts\ServiceContract;
use App\Responses\ResponseService;

abstract class BaseService implements ServiceContract 
{
    protected static function success($data, string $message = "success"): ResponseService 
    {
        return new ResponseService($data, $message, 200);
    }

    protected static function error($data, string $message = "error", int $status = 400): ResponseService 
    {
        return new ResponseService($data, $message, $status);
    }
}
