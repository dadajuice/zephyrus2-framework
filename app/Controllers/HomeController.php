<?php

declare(strict_types=1);

namespace App\Controllers;

use Zephyrus\Controller\Controller;
use Zephyrus\Core\Config\Configuration;
use Zephyrus\Core\Config\DatabaseConfig;
use Zephyrus\Core\Kernel;
use Zephyrus\Data\Database;
use Zephyrus\Http\Response;
use Zephyrus\Rendering\RenderResponses;
use Zephyrus\Routing\Attribute\Get;

final class HomeController extends Controller
{
    use RenderResponses;

    #[Get('/')]
    public function index(): Response
    {
        return $this->render('home', [
            'frameworkVersion' => (new Kernel())->version(),
            'phpVersion' => PHP_VERSION,
            'environment' => env('APP_ENV', 'production'),
            'extensions' => $this->checkExtensions(),
            'database' => $this->checkDatabase(),
        ]);
    }

    /**
     * @return array<string, bool>
     */
    private function checkExtensions(): array
    {
        return [
            'pdo' => extension_loaded('pdo'),
            'pdo_pgsql' => extension_loaded('pdo_pgsql'),
            'intl' => extension_loaded('intl'),
            'sodium' => extension_loaded('sodium'),
            'mbstring' => extension_loaded('mbstring'),
            'fileinfo' => extension_loaded('fileinfo'),
            'curl' => extension_loaded('curl'),
        ];
    }

    /**
     * @return array{connected: bool, message: string, version: string}
     */
    private function checkDatabase(): array
    {
        try {
            $config = \Zephyrus\Core\App::getConfiguration();
            if ($config === null || $config->database === null) {
                return [
                    'connected' => false,
                    'message' => 'No database configured',
                    'version' => '',
                ];
            }

            $db = Database::fromConfig($config->database);
            $version = $db->selectValue("SELECT version()");

            return [
                'connected' => true,
                'message' => 'Connected',
                'version' => is_string($version) ? $version : '',
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'message' => $e->getMessage(),
                'version' => '',
            ];
        }
    }
}
