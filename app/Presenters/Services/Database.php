<?php
/**
 * Access Storage
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
 * Access Storage
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */
class Database
{
    use \Nette\SmartObject; 
    
    /**
     * Constructor of Database access
     *
     * @param \Nette\Database\Connection $connection Database connection
     */
    public function __construct(
        protected \Nette\Database\Connection $connection
    ) {
        
    }

    /**
     * Begin transaction
     *
     * @return void
     */
    public function begin() : void
    {
        $this->connection->beginTransaction();
    }
    
    /**
     * Rollback transaction
     *
     * @return void
     */
    public function rollback()
    {
        $this->connection->rollBack();
    }
    
    /** 
     * Ccommit stransaction
     *
     * @return void
     */
    public function commit()
    {
        $this->connection->commit();
    }
}