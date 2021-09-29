<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Description of EmailModel
 *
 * @author Daniel Hejduk <daniel.hejduk at gmail.com>
 */
class Database
{
    use \Nette\SmartObject; 
    
    public function __construct(
        protected \Nette\Database\Connection $connection
    ) {
        
    }

    public function begin()
    {
        $this->connection->beginTransaction();
    }
    
    public function rollback()
    {
        $this->connection->rollBack();
    }
    
    public function commit()
    {
        $this->connection->commit();
    }
}