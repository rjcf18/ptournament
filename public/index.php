<?php declare(strict_types=1);

define('PROJECT_ROOT', __DIR__ . '/..');

require PROJECT_ROOT . '/vendor/autoload.php';

use PoolTournament\App\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\App\Module\Core\Entrypoint\Yaml\Parser as YamlParser;
use PoolTournament\App\Module\Core\Entrypoint\Routing\Router;

header("Content-Type: application/json");

try {
    $yamlParser = new YamlParser(PROJECT_ROOT . '/config/routing.yml');
    $routerConfigs = $yamlParser->parse();

    $request = Request::createFromGlobals();

    $router = Router::createFromConfig($routerConfigs);
    $route = $router->matchRequestToRoute($request);
    $route->dispatch($request);
} catch (Throwable $throwable) {
    http_response_code(500);

    echo json_encode([
        'code' => 500,
        'error' => $throwable->getTraceAsString()
    ]);
}
