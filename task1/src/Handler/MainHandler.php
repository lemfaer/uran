<?php

namespace App\Handler;

use App\Core\Router;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

class MainHandler implements MiddlewareInterface
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected Container $container;

    /**
     * Controller constructor
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, RequestHandlerInterface $next): Response
    {
        [$handler, $args] = $this->dispatch($request);

        return call_user_func_array($handler, $args);
    }

    /**
     * Dispatch request
     *
     * @return array, [ $handler, $args ]
     */
    protected function dispatch(Request $request): array
    {
        /**
         * @var \App\Core\Router
         */
        $router = $container->get(Router::class);

        $server = $request->getServerParams();
        $uri = $server["REQUEST_URI"];
        $method = $server["REQUEST_METHOD"];

        [$handler, $args] = $router->dispatch($uri, $method);
        [$class, $method] = $handler;

        $controller = $container->get($class);

        return [[$controller, $method], $args];
    }
}
