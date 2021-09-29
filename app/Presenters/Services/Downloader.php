<?php
/**
 * Download information from web
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */

declare(strict_types=1);

namespace App\Services;

use Nette;

/**
 * Download information from web
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */
class Downloader
{

    use \Nette\SmartObject;

    protected \Nette\Caching\Cache $plainCache;
    
    
    /**
     * Downloader constructor
     *
     * @param \Nette\Caching\Storage $storage Cache Storage 
     */
    public function __construct(\Nette\Caching\Storage $storage)
    {
        $this->plainCache = new \Nette\Caching\Cache($storage, 'downloader');
    }

    /**
     * Download content from given URL 
     *
     * @param string|\Nette\Http\Url $url URL to download
     * 
     * @return String content
     * 
     * @throws DownloaderException When fail to download
     */
    protected function get(string|\Stringable $url)
    {
        $url=(string)$url;
        $return = $this->plainCache->load($url);
        if ($return === null) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $curl, CURLOPT_HEADERFUNCTION,
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
            if ($return === false) {
                throw new DownloaderException(
                    "Failed to get $url",
                    curl_errno($curl)
                );
            }
            $dependencies = [
            $this->plainCache::EXPIRATION=> $headers['access-control-max-age'][0]??10
            ];
            $this->plainCache->save($url, $return, $dependencies);
        }
        return $return;
    }

}

/**
 * Exception when trying to download
 *
 * @inheritdoc
 * @category   Index
 * @package    Restaurant_Menu
 * @author     Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license    None https://en.wikipedia.org/wiki/Empty_set
 * @link       https://github.com/Leinad90/restaurant_menu
 */
class DownloaderException extends \Exception
{
    
}
