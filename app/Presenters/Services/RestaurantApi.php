<?php
declare(strict_types=1);

namespace App\Services;

use Nette;
use Nette\Http\UrlImmutable; 
/**
 * Description of RestaurantApi
 *
 * @author Daniel Hejduk <daniel.hejduk at gmail.com>
 */


class RestaurantApi extends Downloader {
	
	private UrlImmutable $urlBase;


	public function __construct(string $urlBase, \Nette\Caching\Storage $storage)
	{
		parent::__construct($storage);
		$this->urlBase = new UrlImmutable($urlBase);
	}

	public function getList()
	{
		$url = $this->urlBase->withPath("restaurant");
		$downloaded = $this->get($url);
		return \Nette\Utils\Json::decode($downloaded);
	}
	
	public function getDetail(int $restaurantId) {
		$url = $this->urlBase->withPath('daily-menu')->withQuery(['restaurant_id'=>$restaurantId]);
		$downloaded = $this->get($url);
		return \Nette\Utils\Json::decode($downloaded);
	}
}
