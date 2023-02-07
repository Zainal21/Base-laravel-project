<?php

namespace App\Base;

abstract class BaseModel
{
    public function toArray(): array 
    {
        return get_object_vars($this);
    }
}