<?php

namespace App\Tests;

use PDO;
use PDOStatement;
use Closure;
use Throwable;
use ArrayObject;
use ReflectionMethod;
use ReflectionProperty;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use App\Factory\ContainerFactory;

use function ob_start;
use function ob_get_clean;

class FunctionalTest extends TestCase
{
    /**
     * Function to create a container
     *
     * @see \App\Factory\ContainerFactory::createContainer
     *
     * @var callable
     */
    protected Closure $create;

    /**
     * Function to set container
     *
     * @var callable
     */
    protected Closure $set;

    /**
     * Echo body
     *
     * @var \Laminas\HttpHandlerRunner\Emitter\EmitterInterface
     */
    protected EmitterInterface $emitter;

    /**
     * Application routes
     *
     * @var array
     */
    protected array $routes;

    /**
     * {@inheritDoc}
     */
    protected function init(): void
    {
        if (!isset($this->routes)) {
            $this->routes = require __DIR__ . "/../etc/routes.php";
        }

        if (!isset($this->create)) {
            $rc = new ReflectionMethod(
                ContainerFactory::class,
                "createContainer"
            );

            $rc->setAccessible(true);

            $this->create = $rc->getClosure();
        }

        if (!isset($this->set)) {
            $rp = new ReflectionProperty(
                ContainerFactory::class,
                "container"
            );

            $rp->setAccessible(true);

            $this->set = Closure::fromCallable([$rp, "setValue"]);
        }

        if (!isset($this->emitter)) {
            $this->emitter = new class() implements EmitterInterface {
                public function emit(Response $response): bool {
                    echo $response->getBody();
                    return true;
                }
            };
        }
    }

    /**
     * Test application runtime result
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param array $server server params
     * @param array $data db data
     * @param mixed $expected
     *
     * @dataProvider provider
     */
    public function test(Container $container, array $server, array $data, $expected): void
    {
        $this->init();
        ($this->set)($container);

        $temp = $_SERVER;
        $_SERVER = array_merge($_SERVER, $server);

        try {
            ob_start();
            require __DIR__ . "/../public/index.php";
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            $output = [
                "code" => $e->getCode(),
                "message" => $e->getMessage(),
            ];
        }

        $_SERVER = $temp;

        $this->assertSame($expected, $output);
    }

    /**
     * General provider
     */
    public function provider(): array
    {
        $this->init();

        return [
            "not_allowed" => $this->notAllowedProvider(),
            "hello" => $this->helloProvider(),
            "page_list" => $this->pageListProvider(),
            "single_page" => $this->singlePageProvider(),
        ];
    }

    public function helloProvider(): array
    {
        return [
            "container" => ($this->create)([
                "config" => [],
                "routes" => $this->routes,
                EmitterInterface::class => $this->emitter,
            ]),
            "server" => [
                "REQUEST_URI" => "/hello/",
                "REQUEST_METHOD" => "GET",
            ],
            "data" => [],
            "result" => "hello",
        ];
    }

    public function notAllowedProvider(): array
    {
        return [
            "container" => ($this->create)([
                "config" => [],
                "routes" => $this->routes,
                EmitterInterface::class => $this->emitter,
            ]),
            "server" => [
                "REQUEST_URI" => "/z",
                "REQUEST_METHOD" => "GET",
            ],
            "data" => [],
            "result" => [
                "code" => 404,
                "message" => "Method not allowed"
            ],
        ];
    }

    public function pageListProvider(): array
    {
        $data = [
            [
                "id" => 1,
                "friendly" => null,
                "title" => "Jimmy",
                "description" => "",
            ],
            [
                "id" => 2,
                "friendly" => null,
                "title" => "Larry",
                "description" => "",
            ],
            [
                "id" => 3,
                "friendly" => null,
                "title" => "Ellen",
                "description" => "",
            ]
        ];

        $pdo = $this
            ->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->setMethods(["prepare"])
            ->getMock();

        $statm = $this
            ->getMockBuilder(ArrayObject::class)
            ->disableOriginalConstructor()
            ->setMethods(["execute"])
            ->getMock();

        $pdo
            ->expects($this->any())
            ->method("prepare")
            ->will(
                $this->returnValue($statm)
            );

        $statm
            ->expects($this->any())
            ->method("execute")
            ->will(
                $this->returnValue(true)
            );

        $statm->exchangeArray($data);

        return [
            "container" => ($this->create)([
                "config" => [],
                "routes" => $this->routes,
                EmitterInterface::class => $this->emitter,
                PDO::class => $pdo,
            ]),
            "server" => [
                "REQUEST_URI" => "/",
                "REQUEST_METHOD" => "GET",
            ],
            "data" => $data,
            "result" => json_encode(array_column($data, null, "id")),
        ];
    }

    public function singlePageProvider(): array
    {
        $data = [
            [
                "id" => 4,
                "friendly" => 5,
                "title" => "test1",
                "description" => ""
            ],
            [
                "id" => 5,
                "friendly" => 4,
                "title" => "test2",
                "description" => ""
            ]
        ];

        $pdo = $this
            ->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->setMethods(["prepare"])
            ->getMock();

        $statm = $this
            ->getMockBuilder(ArrayObject::class)
            ->disableOriginalConstructor()
            ->setMethods(["execute"])
            ->getMock();

        $pdo
            ->expects($this->any())
            ->method("prepare")
            ->will(
                $this->returnValue($statm)
            );

        $statm
            ->expects($this->any())
            ->method("execute")
            ->will(
                $this->returnValue(true)
            );

        $statm->exchangeArray($data);

        return [
            "container" => ($this->create)([
                "config" => [],
                "routes" => $this->routes,
                EmitterInterface::class => $this->emitter,
                PDO::class => $pdo,
            ]),
            "server" => [
                "REQUEST_URI" => "/page/4",
                "REQUEST_METHOD" => "GET",
            ],
            "data" => $data,
            "result" => json_encode(
                array_merge(
                    $data[0],
                    ["friendly" => $data[1]]
                )
            ),
        ];
    }
}
