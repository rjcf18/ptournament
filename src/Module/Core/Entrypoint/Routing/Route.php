<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Core\Entrypoint\Routing;

class Route
{
    private string $name;
    private string $url;
    private string $controller;
    private string $action;
    private array $methods;
    private array $parameters = [];

    public function __construct(string $name, string $url, string $handler, array $methods)
    {
        $handler = explode('::', $handler);

        $this->name = $name;
        $this->url = $url;
        $this->controller = $handler[0];
        $this->action = $handler[1] ?? '';
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

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function dispatch(): void
    {
        $controllerInstance = new $this->controller;

        if (empty($this->action) || trim($this->action) === '') {
            $this->action = 'indexAction';
        }

        call_user_func_array([$controllerInstance, $this->action], $this->parameters);
    }
}