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
	
	private string $urlBase;


	public function __construct()
	{
		$this->urlBase = "https://private-anon-d14d2ce8c7-idcrestaurant.apiary-mock.com/";
	}


	public function getList()
	{
		$url = $this->urlBase."restaurant";
		$downloaded = file_get_contents($url);
		return \Nette\Utils\Json::decode($downloaded);
	}
	
	public function getDetail(int $restaurantId) {
		$url = $this->urlBase."daily-menu?restaurant_id=".$restaurantId;
		$downloaded = file_get_contents($url);
		return \Nette\Utils\Json::decode($downloaded);
	}
}
