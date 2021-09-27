<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Description of EmailModel
 *
 * @author Daniel Hejduk <daniel.hejduk at gmail.com>
 */
class EmailModel Extends Database {
	

	public function insert(string $email) : int
	{
		$sql = 'INSERT INTO e_mails (e_mail, hash) VALUES (?, sha1(?))';
		try {
			$this->connection->query($sql, $email, $email);
			return (int)$this->connection->getInsertId();
		} catch (\Nette\Database\UniqueConstraintViolationException $e) {
			throw new EmailModelException("Address Exists", EmailModelException::ERROR_ADDRESS_EXISTS , $e);
		}
	}
	
	public function insertRestaurants(int $email, array $restaurants)
	{
		$sql= 'INSERT INTO email_restaurants (e_mail, restaurant) VALUES (?, ?)';
		foreach ($restaurants as $restaurant)
		{
			$this->connection->query($sql, $email, $restaurant);
		}
	}

}

class EmailModelException Extends \Exception 
{
	const ERROR_ADDRESS_EXISTS = 1; 
}