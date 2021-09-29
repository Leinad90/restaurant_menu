<?php
/**
 * Base index file starting aplication
 * php version 8.0.11
 * @category Index
 * @package Restaurant Menu
 * @author Daniel Hejduk <daniel.hejduk at gmail.com>
 * @licence None
 * @link https://github.com/Leinad90/restaurant_menu
 */
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

App\Bootstrap::boot()
    ->createContainer()
    ->getByType(Nette\Application\Application::class)
    ->run();
