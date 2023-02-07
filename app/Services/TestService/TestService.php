<?php

namespace App\Services\TestService;

use App\Base\BaseService;
use App\Responses\ResponseService;
use Illuminate\Support\Facades\Validator;
use App\Repositories\TestRepository\TestRepository;
use App\Repositories\TestRepository\Models\TestData;

class TestService extends BaseService 
{
    private $data;
    
    public function __construct(TestData $data)
    {
        $this->data = $data;
    }

    protected function validate(): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($this->data->toArray(), [
            "name"     => "required",
            "email"    => "required|email|unique:tests",
            "description" => "required",
        ]);
    }

    public function call() : ResponseService
    {
        if($this->validate()->fails()){
            return self::error($this->validate()->errors(), 'Error Validate');
        }
        $data = (new TestRepository)->createNewData($this->data);
        return self::success($data, 'Create Data Success');
    }

}
