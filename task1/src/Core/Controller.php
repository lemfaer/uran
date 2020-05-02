<?php

namespace App\Core;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;

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
}
