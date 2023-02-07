<?php

namespace App\Base;

use App\Contracts\ServiceContract;
use App\Responses\ServiceResponse;

abstract class BaseService implements ServiceContract 
{
    protected static function success($data, string $message = "success"): ServiceResponse 
    {
        return new ServiceResponse($data, $message, 200);
    }

    protected static function error($data, string $message = "error", int $status = 300): ServiceResponse 
    {
        return new ServiceResponse($data, $message, $status);
    }

}
