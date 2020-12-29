<?php declare(strict_types=1);
namespace UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing;

use PHPUnit\Framework\TestCase;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\ForbiddenRequestMethodException;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\NoRouteFoundException;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Route;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\RouteCollection;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Router;

class RouterTest extends TestCase
{
    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWhenRouteIsFound()
    {
        $router = $this->getRouter();

        $request = new Request('GET', '/test/');

        $expectedRoute = new Route(
            'routeTest',
            '/test/',
            'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
            ['GET']
        );

        $this->assertEquals($expectedRoute, $router->matchRequestToRoute($request));
    }

    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWhenMethodNotAllowed()
    {
        $router = $this->getRouter();

        $request = new Request('DELETE', '/test/');

        $expectedException = new ForbiddenRequestMethodException(
            $request->getMethod(),
            $router->getRoutes()->get('routeTest')->getMethods()
        );

        $this->expectException($expectedException::class);
        $this->expectExceptionMessage($expectedException->getMessage());

        $this->assertNull($router->matchRequestToRoute($request));
    }

    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWhenNoRouteIsFound()
    {
        $router = $this->getRouter();

        $request = new Request('DELETE', '/no/route/');

        $expectedException = new NoRouteFoundException();

        $this->expectException($expectedException::class);
        $this->expectExceptionMessage($expectedException->getMessage());

        $this->assertNull($router->matchRequestToRoute($request));
    }

    public function testSetBasePath()
    {
        $router = new Router(new RouteCollection());
        $router->setBasePath('/root/');

        $this->assertEquals('/root', $router->getBasePath());
    }

    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWithBasePath()
    {
        $router = $this->getRouter();
        $router->setBasePath('/localhost/root');

        $request = new Request('GET', '/localhost/root/test');

        $expectedRoute = new Route(
            'routeTest',
            '/test/',
            'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
            ['GET']
        );

        $this->assertEquals($expectedRoute, $router->matchRequestToRoute($request));
    }

    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWithNamedParameters()
    {
        $router = $this->getRouter();

        $request = new Request('GET', '/test/100');

        $expectedRoute = new Route(
            'routeTestWithParameter',
            '/test/:id',
            'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
            ['GET']
        );

        $this->assertEquals($expectedRoute, $router->matchRequestToRoute($request));

        $expectedNamedParameters = ['id' => '100'];

        $this->assertEquals($expectedNamedParameters, $request->getNamedParameters());
    }

    public function testCreateFromConfigWhenConfigIsCorrect()
    {
        $config = [
            'base_path' => '/root/',
            'routes' => [
                'routeTest' => ['/test/', 'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction', ['GET']]
            ]
        ];

        $expectedRouter = new Router(
            (new RouteCollection())
                ->addRoute(
                    new Route(
                        'routeTest',
                        '/test/',
                        'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
                        ['GET']
                    )
                )
        );
        $expectedRouter->setBasePath('/root');

        $this->assertEquals($expectedRouter, Router::createFromConfig($config));
    }

    private function getRouter(): Router
    {
        $collection = new RouteCollection();
        $collection->addRoute(
            new Route(
                'routeTest',
                '/test/',
                'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
                ['GET']
            )
        );

        $collection->addRoute(
            new Route(
                'routeTestWithParameter',
                '/test/:id',
                'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
                ['GET']
            )
        );

        $collection->addRoute(
            new Route(
                'routeTestIndex',
                '/',
                'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController',
                ['GET']
            )
        );

        return new Router($collection);
    }
}