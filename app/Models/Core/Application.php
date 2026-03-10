<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Controllers\HomeController;
use Zephyrus\Routing\Router;

final class Application extends Kernel
{
    protected function registerControllers(Router $router): void
    {
        $router->controller(HomeController::class);
    }
}
