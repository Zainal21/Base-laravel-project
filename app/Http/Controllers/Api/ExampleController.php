<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ExampleService\ExampleService;

class ExampleController extends Controller
{
    private $exampleService;
    
    public function __construct()
    {
        $this->exampleService = new ExampleService();
    }

    public function example()
    {
        $data = $this->exampleService->call();
        return response()->json($data); 
    }
}
