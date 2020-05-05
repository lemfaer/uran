<?php

return [

    // generates Content-Security-Policy nonce
    "csp_nonce" => base64_encode(random_bytes(20)),

];
