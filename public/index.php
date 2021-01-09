<?php declare(strict_types=1);

define('PROJECT_ROOT', __DIR__ . '/..');

require PROJECT_ROOT . '/vendor/autoload.php';

use DI\Container;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Response;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Router;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\RouterConfig;

header("Content-Type: application/json");

try {
    /** @var Container $container */
    $container = require PROJECT_ROOT . '/config/dependency-injection.php';

    $routerConfigs = RouterConfig::createFromFile(PROJECT_ROOT . '/config/routing.yml');

    $request = Request::createFromGlobals();

    $router = new Router($routerConfigs);
    $route = $router->matchRequestToRoute($request);

    /** @var Response $response */
    $response = $container->call([$route->getController(), $route->getAction()], [$request]);

    http_response_code($response->getCode());

    echo json_encode($response->getBody());
} catch (Throwable $throwable) {
    http_response_code(500);

    echo json_encode([
        'code' => 500,
        'error' => $throwable->getMessage()
    ]);
}
