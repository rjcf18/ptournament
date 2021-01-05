<?php declare(strict_types=1);
namespace UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing;

use PHPUnit\Framework\TestCase;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\ConfigsNotFoundException;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\ConfigsParseException;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Route;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\RouteCollection;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\RouterConfig;

class RouterConfigTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = sprintf('%s/Fixtures', __DIR__);
    }

    /**
     * @throws ConfigsNotFoundException
     * @throws ConfigsParseException
     */
    public function testCreateFromFileWhenFileIsValidReturnsCorrectObject(): void
    {
        $filePath = sprintf('%s/valid.yml', $this->fixturesPath);

        $expectedRouterConfig = new RouterConfig(
            (new RouteCollection())
                ->addRoute(
                    new Route(
                        'test1',
                        '/url1',
                        'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::indexAction',
                        ['GET']
                    )
                )
                ->addRoute(
                    new Route(
                        'test2',
                        '/url2',
                        'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController::testAction',
                        ['GET', 'POST']
                    )
                )
                ->addRoute(
                    new Route(
                        'test3',
                        '/url3',
                        'UnitTest\Application\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures\TestController',
                        ['PUT']
                    )
                )
        );
        $expectedRouterConfig->setBasePath('/base');

        $routerConfig = RouterConfig::createFromFile($filePath);

        $this->assertEquals($expectedRouterConfig, $routerConfig);
    }

    /**
     * @throws ConfigsNotFoundException
     * @throws ConfigsParseException
     */
    public function testCreateFromFileWhenFileNotFoundThrowsException(): void
    {
        $filePath = 'non-existent';

        $this->expectException(ConfigsNotFoundException::class);
        $this->expectExceptionMessage(sprintf('%s (%s)', ConfigsNotFoundException::MESSAGE, $filePath));

        RouterConfig::createFromFile($filePath);
    }

    /**
     * @throws ConfigsNotFoundException
     * @throws ConfigsParseException
     */
    public function testCreateFromFileWhenProblemParsingFileOccursThrowsException(): void
    {
        $filePath = sprintf('%s/invalid.yml', $this->fixturesPath);

        $this->expectException(ConfigsParseException::class);
        $this->expectExceptionMessage(sprintf('%s (%s)', ConfigsParseException::MESSAGE, $filePath));

        RouterConfig::createFromFile($filePath);
    }

    public function testSetBasePathSetsBasePathCorrectly()
    {
        $routerConfig = new RouterConfig(new RouteCollection());
        $routerConfig->setBasePath('/root/');

        $this->assertEquals('/root', $routerConfig->getBasePath());
    }
}