<?php declare(strict_types=1);
namespace UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\ForbiddenRequestMethodException;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\NoRouteFoundException;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Route;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\RouteCollection;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Router;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\RouterConfig;

class RouterTest extends TestCase
{
    private MockObject|RouterConfig $routerConfigMock;

    private Router $router;

    protected function setUp(): void
    {
        $this->routerConfigMock = $this->getRouterConfigMock();
        $this->router = new Router($this->routerConfigMock);
    }

    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWhenRouteIsFound()
    {
        $expectedRoute = new Route(
            'routeTest',
            '/test/',
            'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
            ['GET']
        );

        $this->routerConfigMock
            ->expects($this->once())
            ->method('getRoutes')
            ->willReturn($this->getRouteCollection());

        $this->routerConfigMock
            ->expects($this->once())
            ->method('getBasePath')
            ->willReturn('');

        $request = new Request('GET', '/test/');

        $this->assertEquals($expectedRoute, $this->router->matchRequestToRoute($request));
    }

    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWhenMethodNotAllowed()
    {
        $this->routerConfigMock
            ->expects($this->once())
            ->method('getRoutes')
            ->willReturn($this->getRouteCollection());

        $this->routerConfigMock
            ->expects($this->once())
            ->method('getBasePath')
            ->willReturn('');

        $request = new Request('DELETE', '/test/');

        $expectedException = new ForbiddenRequestMethodException(
            $request->getMethod(),
            $this->getRouteCollection()->get('routeTest')->getMethods()
        );

        $this->expectException($expectedException::class);
        $this->expectExceptionMessage($expectedException->getMessage());

        $this->assertNull($this->router->matchRequestToRoute($request));
    }

    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWhenNoRouteIsFound()
    {
        $this->routerConfigMock
            ->expects($this->once())
            ->method('getRoutes')
            ->willReturn($this->getRouteCollection());

        $this->routerConfigMock
            ->expects($this->once())
            ->method('getBasePath')
            ->willReturn('');

        $request = new Request('DELETE', '/no/route/');

        $expectedException = new NoRouteFoundException();

        $this->expectException($expectedException::class);
        $this->expectExceptionMessage($expectedException->getMessage());

        $this->assertNull($this->router->matchRequestToRoute($request));
    }

    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWithBasePath()
    {
        $this->routerConfigMock
            ->expects($this->once())
            ->method('getRoutes')
            ->willReturn($this->getRouteCollection());

        $this->routerConfigMock
            ->expects($this->once())
            ->method('getBasePath')
            ->willReturn('/localhost/root');

        $request = new Request('GET', '/localhost/root/test');

        $expectedRoute = new Route(
            'routeTest',
            '/test/',
            'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
            ['GET']
        );

        $this->assertEquals($expectedRoute, $this->router->matchRequestToRoute($request));
    }

    /**
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     */
    public function testMatchRequestToRouteWithNamedParameters()
    {
        $this->routerConfigMock
            ->expects($this->once())
            ->method('getRoutes')
            ->willReturn($this->getRouteCollection());

        $this->routerConfigMock
            ->expects($this->once())
            ->method('getBasePath')
            ->willReturn('');

        $request = new Request('GET', '/test/100');

        $expectedRoute = new Route(
            'routeTestWithParameter',
            '/test/:id',
            'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
            ['GET']
        );

        $this->assertEquals($expectedRoute, $this->router->matchRequestToRoute($request));

        $expectedNamedParameters = ['id' => '100'];

        $this->assertEquals($expectedNamedParameters, $request->getNamedParameters());
    }

    private function getRouteCollection(): RouteCollection
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

        return $collection;
    }

    private function getRouterConfigMock(): MockObject|RouterConfig
    {
        return $this->getMockBuilder(RouterConfig::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRoutes', 'getBasePath'])
            ->getMock();
    }
}