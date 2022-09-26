<?php

declare(strict_types=1);

namespace App;

use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use RuntimeException;

class Bootstrap
{
    public static function boot(): Container
    {
        $appDir = dirname(__DIR__);
        $loader = new ContainerLoader($appDir . '/temp', true);
        $class = $loader->load(function ($compiler) use ($appDir) {
            $compiler->loadConfig($appDir . '/config/config.neon');
        });

        $container = new $class();
        if ($container instanceof Container === false) {
            throw new RuntimeException('Unable to create DI container.');
        }

        return $container;
    }
}
