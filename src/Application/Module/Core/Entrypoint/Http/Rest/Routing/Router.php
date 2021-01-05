<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\ForbiddenRequestMethodException;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception\NoRouteFoundException;

class Router
{
    private RouterConfig $routerConfig;

    public function __construct(RouterConfig $routerConfig)
    {
        $this->routerConfig = $routerConfig;
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
        $basePath = $this->routerConfig->getBasePath();

        foreach ($this->routerConfig->getRoutes()->getAll() as $route) {
            $pattern = sprintf('@^%s%s/?$@i', preg_quote($basePath), $this->getRouteRegex($route));

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