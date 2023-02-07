<?php

namespace App\Responses;

use App\Contracts\ResponseContract;

class ResponseService implements ResponseContract 
{
    public function __construct($data, string $message, int $status = 200) 
    {
        $this->status  = $status;
        $this->message = $message;
        $this->data    = $data;
    }

    public function status(): int 
    {
        return $this->status;
    }

    public function message(): string 
    {
        return $this->message;
    }

    public function data() 
    {
        return $this->data;
    }
}
