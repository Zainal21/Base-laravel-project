<?php

namespace App\Repositories\TestRepository;

use App\Models\Test;
use App\Base\BaseHttpService;
use App\Repositories\TestRepository\Models\TestData;

interface TestContract 
{
    public function createNewData(TestData $data);
}

class TestRepository implements TestContract 
{
    public function createNewData(TestData $data) 
    {
        return Test::create([
            'name' => $data->name,
            'email' => $data->email,
            'description' => $data->description,
        ]);
    }
}
