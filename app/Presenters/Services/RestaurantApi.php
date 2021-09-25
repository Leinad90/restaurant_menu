<?php

declare(strict_types=1);

namespace App\Services;

use Nette\Http\UrlImmutable;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Schema\Expect;
use Nette\Schema\Processor;

/**
 * Description of RestaurantApi
 *
 * @author Daniel Hejduk <daniel.hejduk at gmail.com>
 */
class RestaurantApi extends Downloader {

	private UrlImmutable $urlBase;

	public function __construct(string $urlBase, \Nette\Caching\Storage $storage) {
		parent::__construct($storage);
		$this->urlBase = new UrlImmutable($urlBase);
	}

	public function getList() {
		$url = $this->urlBase->withPath("restaurant");
		$downloaded = $this->get($url);
		try {
			$data = Json::decode($downloaded);
		} catch (Utils\JsonException $e) {
			throw new RestaurantApiException(message: "Failed to decode data", previous: $e);
		}
		$schema = Expect::arrayOf(
						Expect::structure([
							'id' => Expect::int()->required(),
							'name' => Expect::string(),
							'address' => Expect::string(),
							'url' => Expect::string(),
							'gps' => Expect::structure([
									'lat' => Expect::float(),
									'lng' => Expect::float(),
							]),
						])
		);
		$normalized = $this->validate($schema, $data);
		return $normalized;
	}

	public function getDetail(int $restaurantId)
	{
		$url = $this->urlBase->withPath('daily-menu')->withQuery(['restaurant_id' => $restaurantId]);
		$downloaded = $this->get($url);
		try {
			$data = Json::decode($downloaded);
		} catch (Utils\JsonException $e) {
			throw new RestaurantApiException(message: "Failed to decode data", previous: $e);
		}
		$schema = Expect::arrayOf(
						Expect::structure([
							'date'=> Expect::string()->pattern('\d{4}\-\d{1,2}-\d{1,2}')->required(),
							'courses' => Expect::arrayOf(
									Expect::structure([
										'course' => Expect::string(),
										'meals' => Expect::arrayOf(
											Expect::structure([
												'name' => Expect::string(),
												'price' => Expect::type('int|float'),
											])
										),
									]),
							),
							'note' => Expect::string(),
					])
				);
		$normalized = $this->validate($schema, $data);
		return $normalized;
	}

	protected function validate($schema, $data) {
		try {
			$processor = new Processor();
			return $processor->process($schema, $data);
		} catch (\Nette\Schema\ValidationException $e) {
			throw new RestaurantApiException("Data not valid", previous: $e);
		}
	}

}

class RestaurantApiException extends \Exception {
	
}
