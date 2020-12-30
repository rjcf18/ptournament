<?php declare(strict_types=1);

define('PROJECT_ROOT', __DIR__ . '/..');

require PROJECT_ROOT . '/vendor/autoload.php';

use DI\Container;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Router;
use PoolTournament\Application\Module\Core\Entrypoint\Yaml\Parser as YamlParser;

header("Content-Type: application/json");

try {
    /** @var Container $container */
    $container = require PROJECT_ROOT . '/config/dependency-injection.php';

    $yamlParser = new YamlParser(PROJECT_ROOT . '/config/routing.yml');
    $routerConfigs = $yamlParser->parse();

    $request = Request::createFromGlobals();

    $router = Router::createFromConfig($routerConfigs);
    $route = $router->matchRequestToRoute($request);

    $container->call([$route->getController(), $route->getAction()], [$request]);
} catch (Throwable $throwable) {
    http_response_code(500);

    echo json_encode([
        'code' => 500,
        'error' => $throwable->getMessage()
    ]);
}
