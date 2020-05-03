<?php

namespace App\Factory;

use App\Core\Router;
use App\Core\Container;
use App\Handler\RequestHandler;
use App\Controller\HelloController;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

class ContainerFactory extends Container
{
    /**
     * Container default entries
     *
     * @return array
     */
    public static function getEntries(): array
    {
        return [
            ResponseFactoryInterface::class => new ResponseFactory(),
            StreamFactoryInterface::class => new StreamFactory(),
            EmitterInterface::class => new SapiEmitter(),
        ];
    }

    /**
     * Key - class name, Value - factory funcion
     *
     * @return array
     */
    public static function getFactories(): array
    {
        return [
            ServerRequestInterface::class => [new RequestFactory(), "create"],
            RequestHandlerInterface::class => [new HandlerFactory(), "create"],
            HelloController::class => [new ControllerFactory(HelloController::class), "create"],
            Router::class => [new RouterFactory(), "create"],
        ];
    }

    /**
     * Create new container
     *
     * @param array $entries container entries
     *
     * @return \Psr\Container\ContainerInterface
     */
    public static function createContainer(array $entries = []): ContainerInterface
    {
        $entries += static::getEntries();
        $factories = static::getFactories();
        $container = new static($entries, $factories);

        $container->entries[ContainerInterface::class] = $container;

        return $container;
    }
}
