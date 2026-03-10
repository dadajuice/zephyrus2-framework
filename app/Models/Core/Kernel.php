<?php

declare(strict_types=1);

namespace App\Models\Core;

use Dotenv\Dotenv;
use Zephyrus\Core\App;
use Zephyrus\Core\ApplicationBuilder;
use Zephyrus\Core\Config\Configuration;
use Zephyrus\Http\Request;
use Zephyrus\Http\Response;
use Zephyrus\Mailer\MailerConfig;
use Zephyrus\Rendering\LatteEngine;
use Zephyrus\Rendering\RenderConfig;
use Zephyrus\Routing\Router;

/**
 * Abstract application kernel that encapsulates the full bootstrap lifecycle.
 *
 * Subclass and override the protected template methods to register your
 * controllers, middleware, and error handlers:
 *
 *   class Application extends Kernel
 *   {
 *       protected function registerControllers(Router $router): void
 *       {
 *           $router->controller(HomeController::class);
 *       }
 *   }
 *
 * Then in index.php:
 *
 *   (new Application())->run();
 */
abstract class Kernel
{
    protected Configuration $config;
    protected LatteEngine $renderEngine;

    public function __construct()
    {
        if (!defined('ROOT_DIR')) {
            define('ROOT_DIR', dirname(__DIR__, 3));
        }
        $this->boot();
    }

    /**
     * Handle the current HTTP request and send the response.
     */
    public function run(): void
    {
        $router = new Router();
        $this->registerControllers($router);

        $builder = ApplicationBuilder::fromConfiguration($this->config)
            ->withRouter($router);

        $builder = $this->configureErrorHandlers($builder);

        $renderEngine = $this->renderEngine;
        $builder = $builder->withControllerFactory(function (string $class) use ($renderEngine): object {
            $controller = new $class();
            if (method_exists($controller, 'setRenderEngine')) {
                $controller->setRenderEngine($renderEngine);
            }
            return $controller;
        });

        $builder = $this->registerMiddleware($builder);

        $app = $builder->build();

        $request = Request::fromGlobals(trustedProxies: $this->config->security->trustedProxies);
        $response = $app->handle($request);
        $response->send();
    }

    /**
     * Register all controllers with the router.
     *
     * Override this method in your Application subclass.
     */
    abstract protected function registerControllers(Router $router): void;

    /**
     * Register global and named middleware.
     *
     * Override to add middleware. Default is a no-op.
     */
    protected function registerMiddleware(ApplicationBuilder $builder): ApplicationBuilder
    {
        return $builder;
    }

    /**
     * Configure custom exception handlers.
     *
     * Override to register custom exception handlers via the builder's
     * kernel builder. Default is a no-op.
     */
    protected function configureErrorHandlers(ApplicationBuilder $builder): ApplicationBuilder
    {
        return $builder;
    }

    /**
     * Bootstrap environment, configuration, and render engine.
     */
    private function boot(): void
    {
        Dotenv::createImmutable(ROOT_DIR)->safeLoad();

        $this->config = Configuration::fromYamlFile(ROOT_DIR . '/config.yml', [
            'render' => RenderConfig::class,
            'mailer' => MailerConfig::class,
        ]);

        /** @var RenderConfig $renderConfig */
        $renderConfig = $this->config->section('render') ?? RenderConfig::fromArray([]);
        $this->renderEngine = $renderConfig->createEngine(ROOT_DIR);
    }
}
