<?php

namespace App\Repositories\ExampleRepository;

use App\Base\BaseHttpService;

interface ExampleContract 
{
    public function getUsers();
}

class ExampleRepository implements ExampleContract 
{
    public function getUsers() 
    {
        return BaseHttpService::post()
            ->setUrl(config('service.dummy'))
            ->setServiceName("example") // set your service inquiry's name
            ->call();
    }
}
