<?php

namespace App\Core;

use SplQueue;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * List of middlewares to run
     *
     * @var \SplQueue
     */
    protected SplQueue $middlewares;

    /**
     * RequestHandler construct
     *
     * @param SplQueue $middlewares
     */
    public function __construct(SplQueue $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middlewares->dequeue()->process($request, $this);
    }
}
