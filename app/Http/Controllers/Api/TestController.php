<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Responses\ResponseService;
use App\Http\Controllers\Controller;
use App\Services\TestService\TestService;
use App\Repositories\TestRepository\Models\TestData;

class TestController extends Controller
{
    public function example(Request $request)
    {
        $data = new TestData;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->description = $request->description;
        $action = (new TestService($data))->call();
        return response()->json($action, property_exists($action, 'status') ? $action->status : 200);
    }
}
