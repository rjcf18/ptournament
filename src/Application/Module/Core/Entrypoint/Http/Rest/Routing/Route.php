<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;

class Route
{
    private const DEFAULT_ACTION = 'indexAction';

    private string $name;
    private string $url;
    private string $controller;
    private string $action;
    private array $methods;

    public function __construct(string $name, string $url, string $handler, array $methods)
    {
        $handler = explode('::', $handler);

        $this->name = $name;
        $this->url = $url;
        $this->controller = $handler[0];
        $this->action = $handler[1] ?? self::DEFAULT_ACTION;
        $this->methods = $methods;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getController(): mixed
    {
        return $this->controller;
    }

    public function getAction(): mixed
    {
        return $this->action;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }
}