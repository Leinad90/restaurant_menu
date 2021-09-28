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
	
	public function unsubscribe(int $emailId)
	{
		$sql = 'UPDATE e_mails SET e_mail = NULL WHERE id = ?';
		$this->connection->query($sql,$emailId);
	}


	public function insertRestaurants(int $email, array $restaurants)
	{
		$sql= 'INSERT INTO email_restaurants (e_mail, restaurant) VALUES (?, ?)';
		foreach ($restaurants as $restaurant)
		{
			$this->connection->query($sql, $email, $restaurant);
		}
	}
	
	public function getMailsToSend() : array
	{
		$sql = 'SELECT * FROM e_mails WHERE e_mail IS NOT NULL AND last_send_on < CURRENT_DATE ORDER BY last_send_on ASC';
		return $this->connection->fetchAll($sql);
	}
	
	public function setSentNow(int $emaiId)
	{
		$sql = 'UPDATE e_mails SET last_send_on = now() WHERE id = ?';
		$this->connection->query($sql,$emaiId);
	}

}

class EmailModelException Extends \Exception 
{
	const ERROR_ADDRESS_EXISTS = 1; 
}