<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;
	
	public static function createRouter(Nette\DI\Container $container): RouteList
	{
		$router = new RouteList;
		if(php_sapi_name()==='cli') {
			$router[] = new \Nette\Application\Routers\CliRouter(array('action' => 'Cli:default'));
		} else {
			$router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
		}
		return $router;
	}
}
