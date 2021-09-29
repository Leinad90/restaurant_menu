<?php
/**
 * Download information about restauration
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

use Nette\Http\UrlImmutable;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Schema\Expect;
use Nette\Schema\Processor;

/**
 * Download information about restauration
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */
class RestaurantApi extends Downloader
{

    private UrlImmutable $_urlBase;
    protected \Nette\Caching\Cache $dataCache;

    /**
     * Constructor of API
     *
     * @param string                 $urlBase URL of service
     * @param \Nette\Caching\Storage $storage Caching service
     */
    public function __construct(string $urlBase, \Nette\Caching\Storage $storage)
    {
        parent::__construct($storage);
        $this->dataCache = new \Nette\Caching\Cache($storage, 'data');
        $this->_urlBase = new UrlImmutable($urlBase);
    }

    /**
     * Get list of all restaurants
     *
     * @return array list of restaurant
     * @throws RestaurantApiException When failed to download
     */
    public function getList() : array
    {
        return $this->dataCache->load(
            'restaurants', function () {
                return $this->_downloadList();
            }
        );
    }

    /**
     * Download list of all restaurants
     *
     * @return array list of restaurant
     * @throws RestaurantApiException When failed to download
     */
    private function _downloadList() : array
    {
        $url = $this->_urlBase->withPath("restaurant");
        try {
            $downloaded = $this->get($url);
            $data = Json::decode($downloaded);
        } catch (JsonException $e) {
            throw new RestaurantApiException(
                message: "Failed to decode data",
                previous: $e
            );
        }
        $schema = Expect::arrayOf(
            Expect::structure(
                [
                            'id' => Expect::int()->required(),
                            'name' => Expect::string(),
                            'address' => Expect::string(),
                            'url' => Expect::string(),
                            'gps' => Expect::structure(
                                [
                                'lat' => Expect::float(),
                                'lng' => Expect::float(),
                                ]
                            ),
                            ]
            )
        );
        $normalized = $this->validate($schema, $data);
        $return = array();
        foreach ($normalized as $restaurant) {
            $return[$restaurant->id] = $restaurant;
        }
        return $return;
    }

    /**
     * List for Checkbox
     *
     * @return string[] names indexed by restaurant id
     */
    public function getListForCheckbox():array
    {
        $restaurants = $this->getList();
        $return = [];
        foreach ($restaurants as $restaurant) {
            $return[$restaurant->id] = $restaurant->name;
        }
        return $return;
    }

    /**
     * Get menu of givent restaurant 
     *
     * @param  int $restaurantId ID in api
	 * 
     * @return mixed
     * @throws RestaurantApiException WHen failed to download
     */
    public function getMenu(int $restaurantId)
    {
        $url = $this
            ->_urlBase
            ->withPath('daily-menu')
            ->withQuery(['restaurant_id' => $restaurantId]);
        $downloaded = $this->get($url);
        try {
            $data = Json::decode($downloaded);
        } catch (JsonException $e) {
            throw new RestaurantApiException(
                message: "Failed to decode data",
                previous: $e
            );
        }
        $schema = Expect::arrayOf(
            Expect::structure(
                [
                            'date' => Expect::string()->
                                pattern('\d{4}\-\d{1,2}-\d{1,2}')
                                ->required(),
                            'courses' => Expect::arrayOf(
                                Expect::structure(
                                    [
                                        'course' => Expect::string(),
                                        'meals' => Expect::arrayOf(
                                            Expect::structure(
                                                [
                                                    'name' => Expect::string()
                                                            ->required(),
                                                    'price' => Expect::type(
                                                        'int|float|string'
                                                    )->required(),
                                                    ]
                                            )
                                        ),
                                        ]
                                ),
                            ),
                            'note' => Expect::string(),
                            ]
            )
        );
        $normalized = $this->validate($schema, $data);
        return $normalized;
    }

    /**
     * Get detail information about given restaurant
     *
     * @param int $restaurantId id in api
     * 
     * @return \stdClass data
     */
    public function getDetail(int $restaurantId)
    {
        return $this->dataCache->load(
            $restaurantId,
            function () use ($restaurantId) {
                return $this->getList()[$restaurantId];
            }
        );
    }
    
    /**
     * Validate given data to structure
     *
     * @param \Nette\Schema\Schema $schema Data structure
     * @param type                 $data   Data to test
     * 
     * @return mixed normalized data
     * @throws RestaurantApiException When not valid
     */
    protected function validate(\Nette\Schema\Schema $schema, $data)
    {
        try {
            $processor = new Processor();
            return $processor->process($schema, $data);
        } catch (\Nette\Schema\ValidationException $e) {
            throw new RestaurantApiException("Data not valid", previous: $e);
        }
    }

}

/**
 * When download failed
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */
class RestaurantApiException extends DownloaderException
{
    
}
