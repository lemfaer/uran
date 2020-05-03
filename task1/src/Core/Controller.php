<?php

namespace App\Core;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;

class Controller
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected Container $container;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected Request $request;

    /**
     * Controller constructor
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function __construct(Container $container, Request $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function response(int $code = 200, string $body = '')
    {
        return $this->container
            ->get(ResponseFactory::class)
            ->createResponse($code)
            ->withBody(
                $this->container
                    ->get(StreamFactory::class)
                    ->createStream($body)
            );
    }
}
