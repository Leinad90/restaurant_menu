<?php
/**
 * Route requests to presenter
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk at gmail.com>
 * @licence  None
 * @link     https://github.com/Leinad90/restaurant_menu
 */

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;

/**
 * Bootstrap class
 */
class Bootstrap
{
    /**
     * Get Configuration
     *
     * @return Configurator
     */
    public static function boot(): Configurator
    {
        $configurator = new Configurator;
        $appDir = dirname(__DIR__);

        $configurator->enableTracy($appDir . '/log');

        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory($appDir . '/temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $configurator->addConfig($appDir . '/config/common.neon');
        $configurator->addConfig($appDir . '/config/local.neon');

        return $configurator;
    }
}
