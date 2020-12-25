<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Core\Entrypoint\Routing;

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

    public function run(): bool|Route
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUrl = $_SERVER['REQUEST_URI'];

        $requestUrl = $this->stripUrlEncodedParamsFromUrl($requestUrl);

        return $this->matchRequestToRoute($requestUrl, $requestMethod);
    }

    private function stripUrlEncodedParamsFromUrl(mixed $requestUrl): string
    {

        if ($this->urlHasEncodedParameters($requestUrl)) {
            $requestUrl = strtok($requestUrl, '?');
        }

        return $requestUrl;
    }

    private function urlHasEncodedParameters(mixed $requestUrl): bool
    {
        return str_contains($requestUrl, '?');
    }

    /**
     * @param string $requestUrl
     * @param string $requestMethod
     *
     * @throws ForbiddenRequestMethodException
     * @throws NoRouteFoundException
     *
     * @return Route
     */
    private function matchRequestToRoute(string $requestUrl, string $requestMethod): Route
    {
        foreach ($this->routes->getAll() as $route) {
            if (!$this->requestMethodIsAllowed($requestMethod, $route)) {
                throw new ForbiddenRequestMethodException($requestMethod, $route->getMethods());
            }

            $pattern = sprintf('@^%s%s/?$@i', preg_quote($this->basePath), $route->getUrl());

            if (!preg_match($pattern, $requestUrl, $matches)) {
                continue;
            }

            $params = [];

            if (preg_match_all('/:([\w-%]+)/', $route->getUrl(), $matches)) {
                $parametersFound = $matches[1];
                $matchesFound = count($matches) - 1;

                if (count($parametersFound) !== $matchesFound) {
                    continue;
                }

                foreach ($parametersFound as $key => $name) {
                    if (isset($matches[$key+1])) {
                        $params[$name] = $matches[$key+1];
                    }
                }
            }

            $route->setParameters($params);
            $route->dispatch();

            return $route;
        }

        throw new NoRouteFoundException();
    }

    private function requestMethodIsAllowed(string $requestMethod, Route $route): bool
    {
        return in_array($requestMethod, $route->getMethods(), true);
    }
}