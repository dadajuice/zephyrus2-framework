<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use Dotenv\Dotenv;
use Zephyrus\Core\App;
use Zephyrus\Core\ApplicationBuilder;
use Zephyrus\Core\Config\Configuration;
use Zephyrus\Http\Request;
use Zephyrus\Mailer\MailerConfig;
use Zephyrus\Rendering\RenderConfig;
use Zephyrus\Rendering\RenderResponses;
use Zephyrus\Routing\Router;

define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR . '/vendor/autoload.php';

// 1. Load environment variables from .env
Dotenv::createImmutable(ROOT_DIR)->safeLoad();

// 2. Load typed configuration with custom section factories
$config = Configuration::fromYamlFile(ROOT_DIR . '/config.yml', [
    'render' => RenderConfig::class,
    'mailer' => MailerConfig::class,
]);

// 3. Create the render engine from config
$renderConfig = $config->section('render') ?? RenderConfig::fromArray([]);
$renderEngine = $renderConfig->createEngine(ROOT_DIR);

// 4. Register global services for helper functions (config(), nonce(), etc.)
App::setConfiguration($config);

// 5. Define routes
$router = (new Router())
    ->controller(HomeController::class);

// 6. Build application
$app = ApplicationBuilder::fromConfiguration($config)
    ->withRouter($router)
    ->withControllerFactory(function (string $class) use ($renderEngine): object {
        $controller = new $class();
        if (method_exists($controller, 'setRenderEngine')) {
            $controller->setRenderEngine($renderEngine);
        }
        return $controller;
    })
    ->build();

// 7. Handle request and send response
$request = Request::fromGlobals(trustedProxies: $config->security->trustedProxies);
$response = $app->handle($request);
$response->send();
