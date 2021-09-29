<?php
/**
 * Route requests to presenter
 * php version 8.0.11
 * @category Index
 * @package Restaurant Menu
 * @author Daniel Hejduk <daniel.hejduk at gmail.com>
 * @licence None
 * @link https://github.com/Leinad90/restaurant_menu
 */

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


/**
 * Router factory
 */
final class RouterFactory
{
    use Nette\StaticClass;
    
	/**
	 * Creates route list
	 * @param Nette\DI\Container $container Containter 
	 * @return RouteList
	 */
    public static function createRouter(Nette\DI\Container $container): RouteList
    {
        $router = new RouteList;
        if(php_sapi_name()==='cli')
		{
            $router[] = new \Nette\Application\Routers\CliRouter(
					array('action' => 'Cli:default')
			);
        } else {
            $router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
        }
        return $router;
    }
}
