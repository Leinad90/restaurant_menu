<?php
/**
 * Access Email Storage
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
 * Access Email Storage
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */
class EmailModel Extends Database
{
    
    /**
     * Instert new subsriber
     *
     * @param string $email pepa@example.com
     * 
     * @return int ID of created subsriber
     * @throws EmailModelException When address exists (or has gdpr thombstone)
     */
    public function insert(string $email) : int
    {
        $sql = 'INSERT INTO e_mails (e_mail, hash) VALUES (?, sha1(?))';
        try {
            $this->connection->query($sql, $email, $email);
            return (int)$this->connection->getInsertId();
        } catch (\Nette\Database\UniqueConstraintViolationException $e) {
            throw new EmailModelException(
                "Address Exists",
                EmailModelException::ERROR_ADDRESS_EXISTS,
                $e
            );
        }
    }
    
    /**
     * Unsubsribe 
     *
     * @param int $emailId id of address to unsubsribe
     * 
     * @return void 
     */
    public function unsubscribe(int $emailId)
    {
        $sql = 'UPDATE e_mails SET e_mail = NULL WHERE id = ?';
        $this->connection->query($sql, $emailId);
    }

    /**
     * Add Restaurants to given subscriber
     * 
     * @param int   $email       ID of subscriber
     * @param int[] $restaurants IDS of restaurants
     * 
     * @return void
     */
    public function insertRestaurants(int $email, array $restaurants) : void
    {
        $sql= 'INSERT INTO email_restaurants (e_mail, restaurant) VALUES (?, ?)';
        foreach ($restaurants as $restaurant) {
            $this->connection->query($sql, $email, $restaurant);
        }
    }
    
    /**
     * Address to send emails
     *
     * @return \Nette\Database\Row[] Array of adresses
     */
    public function getMailsToSend() : array
    {
        $sql = 'SELECT * FROM e_mails
WHERE e_mail IS NOT NULL
AND last_send_on < CURRENT_DATE
ORDER BY last_send_on ASC';
        return $this->connection->fetchAll($sql);
    }
    
    /**
     * Set than mails to this address was sent
     *
     * @param int $emaiId address id
     * 
     * @return void
     */
    public function setSentNow(int $emaiId) : void
    {
        $sql = 'UPDATE e_mails SET last_send_on = now() WHERE id = ?';
        $this->connection->query($sql, $emaiId);
    }

}

/**
 * Exception when access Email Storage
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */
class EmailModelException Extends \Exception
{
    const ERROR_ADDRESS_EXISTS = 1; 
}