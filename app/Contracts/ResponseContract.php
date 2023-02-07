<?php

namespace App\Contracts;

interface ResponseContract 
{
    public function status() : int;
    public function message() : string;
    public function data();
}
