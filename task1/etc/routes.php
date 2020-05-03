<?php

use App\Controller as c;

return [
    [ "GET", "~^/hello$~", [ c\HelloController::class, "greet" ] ],
];
