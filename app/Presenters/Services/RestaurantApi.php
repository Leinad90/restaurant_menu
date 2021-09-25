<?php
declare(strict_types=1);

namespace App\Services;

use Nette;
/**
 * Description of RestaurantApi
 *
 * @author Daniel Hejduk <daniel.hejduk at gmail.com>
 */


class RestaurantApi {
    use Nette\SmartObject; 
    public function getList()
	{
		$url = "https://private-anon-d14d2ce8c7-idcrestaurant.apiary-mock.com/restaurant";
		$downloaded = file_get_contents($url);
		return \Nette\Utils\Json::decode($downloaded);
	}
}
