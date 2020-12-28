<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Core\Entrypoint\Http\Rest\Routing;

class RouteCollection
{
    /** @var Route[] */
    private array $routes = [];

    public static function create(): self
    {
        return new self();
    }

    public function addRoute(Route $route): self
    {
        $this->routes[$route->getName()] = $route;

        return $this;
    }

    public function getAll(): array
    {
        return $this->routes;
    }

    public function get(string $name): ?Route
    {
        return $this->routes[$name] ?? null;
    }
}