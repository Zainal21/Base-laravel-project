<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ExampleService\ExampleService;

class ExampleController extends Controller
{
    public function example()
    {
        $data = (new ExampleService)->call();
        return response()->json($data); 
    }
}
