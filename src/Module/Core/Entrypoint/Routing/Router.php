<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Core\Entrypoint\Routing;

use PoolTournament\App\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\App\Module\Core\Entrypoint\Routing\Exception\ForbiddenRequestMethodException;
use PoolTournament\App\Module\Core\Entrypoint\Routing\Exception\NoRouteFoundException;

class Router
{
    private const CONFIG_FIELD_BASE_PATH = 'base_path';
    private const CONFIG_FIELD_ROUTES = 'routes';

    private RouteCollection $routes;
    private string $basePath = '';

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public static function createFromConfig(array $routesConfig): self
    {
        $routeCollection = RouteCollection::create();

        foreach ($routesConfig[self::CONFIG_FIELD_ROUTES] as $name => $route) {
            $routeCollection->addRoute(
                new Route(
                    name: $name,
                    url: $route[0],
                    handler: $route[1],
                    methods: (array) $route[2]
                )
            );
        }

        $router = new self($routeCollection);

        if (isset($routesConfig[self::CONFIG_FIELD_BASE_PATH])) {
            $router->setBasePath($routesConfig[self::CONFIG_FIELD_BASE_PATH]);
        }

        return $router;
    }

    public function setBasePath($basePath): void
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * @param Request $request
     *
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     *
     * @return Route
     */
    public function matchRequestToRoute(Request $request): Route
    {
        foreach ($this->routes->getAll() as $route) {
            $pattern = sprintf('@^%s%s/?$@i', preg_quote($this->basePath), $this->getRouteRegex($route));

            if (!preg_match($pattern, $request->getUri(), $matches)) {
                continue;
            }

            if (!$this->requestMethodIsAllowed($request->getMethod(), $route)) {
                throw new ForbiddenRequestMethodException($request->getMethod(), $route->getMethods());
            }

            $params = [];

            if (preg_match_all('/:(\w+)/', $route->getUrl(), $namedParametersNames)) {
                array_shift($matches);
                $namedParametersValues = $matches;
                $namedParametersNames = $namedParametersNames[1];

                if (count($namedParametersNames) !== count($namedParametersValues)) {
                    continue;
                }

                foreach ($namedParametersNames as $key => $name) {
                    if (isset($namedParametersValues[$key])) {
                        $params[$name] = $namedParametersValues[$key];
                    }
                }
            }

            $request->setNamedParameters($params);

            return $route;
        }

        throw new NoRouteFoundException();
    }

    private function getRouteRegex(Route $route): string
    {
        return rtrim(preg_replace('/(:\w+)/', '(\w+)', $route->getUrl()), '/');
    }

    private function requestMethodIsAllowed(string $requestMethod, Route $route): bool
    {
        return in_array($requestMethod, $route->getMethods(), true);
    }
}