<?php

namespace App\Contracts;

use App\Responses\ResponseService;

interface ServiceContract 
{
    public function call(): ResponseService;

}
