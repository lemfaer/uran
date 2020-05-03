<?php

namespace App\Controller;

use App\Core\Controller;

class HelloController extends Controller
{
    public function greet()
    {
        return $this->response(200, "hello");
    }
}
