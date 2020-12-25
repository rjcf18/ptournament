<?php declare(strict_types=1);

define('PROJECT_ROOT', __DIR__ . '/..');

require PROJECT_ROOT . '/vendor/autoload.php';

use PoolTournament\App\Module\Core\Entrypoint\Yaml\Parser as YamlParser;
use PoolTournament\App\Module\Core\Entrypoint\Routing\Router;

try {
    $yamlParser = new YamlParser(PROJECT_ROOT . '/config/routing.yml');
    $routerConfigs = $yamlParser->parse();

    $router = Router::createFromConfig($routerConfigs);
    $router->run();
} catch (Throwable $throwable) {
    http_response_code(500);

    echo $throwable->getMessage();
}
