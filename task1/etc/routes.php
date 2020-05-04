<?php

use App\Controller as c;

return [
    [ "GET", "~^$~", [ c\PageController::class, "list" ] ],
    [ "GET", "~^/page/(\d+)$~", [ c\PageController::class, "single" ] ],
    [ "GET", "~^/hello$~", [ c\HelloController::class, "greet" ] ],
];
