<?php

namespace App\Factory;

use App\Core\Container;
use App\Controller\HelloController;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Laminas\Diactoros\ResponseFactory;

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
            ResponseFactoryInterface::class => new ResponseFactory,
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
            ServerRequestInterface::class => [new RequestFactory, "create"],
            HelloController::class => [new ControllerFactory(HelloController::class), "create"],
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
