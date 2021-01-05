<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\ConfigsNotFoundException;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\ConfigsParseException;

class RouterConfig
{
    private const CONFIG_FIELD_BASE_PATH = 'base_path';
    private const CONFIG_FIELD_ROUTES = 'routes';
    private const CONFIG_FIELD_ROUTE_URL = 0;
    private const CONFIG_FIELD_ROUTE_HANDLER = 1;
    private const CONFIG_FIELD_ROUTE_METHODS = 2;

    private RouteCollection $routes;
    private string $basePath = '';

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param string $filePath
     *
     * @throws ConfigsNotFoundException
     * @throws ConfigsParseException
     *
     * @return self
     */
    public static function createFromFile(string $filePath): self
    {
        if (!is_file($filePath)) {
            throw new ConfigsNotFoundException($filePath);
        }

        $parseResult = yaml_parse_file($filePath);

        if (empty($parseResult)) {
            throw new ConfigsParseException($filePath);
        }

        $routesConfig = (array) $parseResult;

        $routeCollection = RouteCollection::create();

        foreach ($routesConfig[self::CONFIG_FIELD_ROUTES] as $name => $route) {
            $routeCollection->addRoute(
                new Route(
                    name: $name,
                    url: $route[self::CONFIG_FIELD_ROUTE_URL],
                    handler: $route[self::CONFIG_FIELD_ROUTE_HANDLER],
                    methods: (array) $route[self::CONFIG_FIELD_ROUTE_METHODS]
                )
            );
        }

        $router = new self($routeCollection);

        if (isset($routesConfig[self::CONFIG_FIELD_BASE_PATH])) {
            $router->setBasePath($routesConfig[self::CONFIG_FIELD_BASE_PATH]);
        }

        return $router;
    }

    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function setBasePath(string $basePath): void
    {
        $this->basePath = rtrim($basePath, '/');
    }
}