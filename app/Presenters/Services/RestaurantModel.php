<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Description of EmailModel
 *
 * @author Daniel Hejduk <daniel.hejduk at gmail.com>
 */
class RestaurantModel Extends Database {
	

	public function insertUpdate(string $name, int $apiId) : int
	{
		$sql = 'INSERT INTO restaurants (api_id, name) VALUES (?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name)';
		$this->connection->query($sql, $apiId, $name);
		return (int)$this->connection->getInsertId();
	}

}
