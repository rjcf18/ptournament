<?php declare(strict_types=1);
namespace UnitTest\Module\Core\Entrypoint\Http\Rest\Routing;

use PHPUnit\Framework\TestCase;
use PoolTournament\App\Module\Core\Entrypoint\Http\Rest\Routing\Route;

class RouteTest extends TestCase
{
    private Route $route;

    protected function setUp(): void
    {
        $this->route = new Route(
            'routeTestWithParameters',
            '/test/:id',
            'UnitTest\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
            ['GET']
        );
    }

    public function testGetUrl()
    {
        $this->assertEquals('/test/:id', $this->route->getUrl());
    }

    public function testGetMethods()
    {
        $this->assertEquals(['GET'], $this->route->getMethods());
    }

    public function testGetName()
    {
        $this->assertEquals('routeTestWithParameters', $this->route->getName());
    }

    public function testGetController()
    {
        $this->assertEquals(
            'UnitTest\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController',
            $this->route->getController()
        );
    }

    public function testGetAction()
    {
        $this->assertEquals('testAction', $this->route->getAction());
    }

    public function testGetAction()
    {
        $this->assertEquals('testAction', $this->route->getAction());
    }
}