<?php
/**
 * Access Restaurant Storage
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

/**
 * Access Restaurant Storage
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */
class RestaurantModel Extends Database
{
    
    /**
     * Insert or update information about restaurant
     *
     * @param string $name  Name of restaurant
     * @param int    $apiId Foreing id of restaurant
     * 
     * @return int Our id of restaurant
     */
    public function insertUpdate(string $name, int $apiId) : int
    {
        $sql
            = 'INSERT INTO restaurants (api_id, name)
				VALUES (?, ?)
				ON DUPLICATE KEY UPDATE name=VALUES(name)';
        $this->connection->query($sql, $apiId, $name);
        return (int)$this->connection->getInsertId();
    }
    
    /**
     * Give restaurant list for given subsriber
     *
     * @param int $mailId ID of mail address
     * 
     * @return \Nette\Database\Row[] Restaurant List
     */
    public function getRestaurantsForMail(int $mailId) : array
    {
        $sql = 'SELECT * FROM email_restaurants WHERE e_mail = ?';
        return $this->connection->fetchAll($sql, $mailId);
    }

}
