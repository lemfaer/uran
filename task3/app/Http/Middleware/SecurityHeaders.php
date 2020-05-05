<?php

namespace App\Http\Middleware;

use Closure;

class SecurityHeaders
{
    protected string $csp_nonce;

    public function __construct()
    {
        $this->csp_nonce = config("extra.csp_nonce");
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // prevent browsers allowing content that looks like JavaScript to execute
        $response->headers->set(
            "X-Content-Type-Options",
            "'nosniff'"
        );

        // set Content-Security-Policy header for XSS prevention
        $response->headers->set(
            "Content-Security-Policy",
            implode(' ', [
                // these will block all internal/external scripts without nonce
                "script-src 'strict-dynamic' 'nonce-{$this->csp_nonce}'",

                // refusing to be framed by another site, prevent clickjacking
                "frame-ancestors 'none'"
            ])
        );

        return $response;
    }
}
