<?php

namespace App\Services\ExampleService;

use App\Base\BaseService;
use App\Repositories\ExampleRepository\ExampleRepository;
use App\Responses\ResponseService;

class ExampleService extends BaseService 
{
    public function call(): ResponseService 
    {
        $users = (new ExampleRepository)->getUsers();
        if ($users->status() != 200) {
            return self::error(null, $users->message());
        }

        return self::success($users->data());
    }
}
