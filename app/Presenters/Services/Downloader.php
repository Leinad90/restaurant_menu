<?php

declare(strict_types=1);

namespace App\Services;

use Nette;

/**
 * Description of RestaurantApi
 *
 * @author Daniel Hejduk <daniel.hejduk at gmail.com>
 */
class Downloader {

	use \Nette\SmartObject;

	protected \Nette\Caching\Cache $plainCache;

	public function __construct(\Nette\Caching\Storage $storage) {
		$this->plainCache = new \Nette\Caching\Cache($storage, 'downloader');
	}

	protected function get(string|\Stringable $url) {
		$return = $this->plainCache->load($url);
		if ($return === null) {
			$curl = curl_init((string) $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADERFUNCTION,
					function ($curl, $header) use (&$headers) {
						$len = strlen($header);
						$header = explode(':', $header, 2);
						if (count($header) < 2) { 
							return $len;
						}
						$headers[strtolower(trim($header[0]))][] = trim($header[1]);
						return $len;
					}
			);
			$return = curl_exec($curl);
			if($return === false) {
				throw new DownloaderException("Failed to get $url",curl_errno($curl));
			}
			$dependencies = [$this->plainCache::EXPIRATION=> $headers['access-control-max-age'][0]];
			$this->plainCache->save($url, $return, $dependencies);
		}
		return $return;
	}

}

class DownloaderException extends \Exception {
	
}
