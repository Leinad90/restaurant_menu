<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

class CliPresenter extends Nette\Application\UI\Presenter {

	public function __construct(
			private \App\Services\RestaurantModel $RestaurantModel,
			private \App\Services\RestaurantApi $RestaurantApi,
			private \App\Services\EmailModel $EmailModel,
			private \Nette\Mail\Mailer $Mailer
	) {
		parent::__construct();
	}

	public function renderDefault() {
		$emails = $this->EmailModel->getMailsToSend();
		foreach ($emails as $email) {
			$restaurants = $this->RestaurantModel->getRestaurantsForMail($email->id);
			$menus = $details = [];
			foreach ($restaurants as $restaurant) {
				$details[] = $this->RestaurantApi->getDetail($restaurant->restaurant);
				$menus[] = $this->RestaurantApi->getMenu($restaurant->restaurant);
			}
			$Message = $this->buildMessage($details, $menus, $email);
			$this->Mailer->send($Message);
			$this->EmailModel->setSentNow($email->id);
		}
		$this->terminate();
	}

	private function buildMessage(array $details, array $menus, \Nette\Database\Row $email): \Nette\Mail\Message {
		$Latte = new \Latte\Engine();
		$Message = new \Nette\Mail\Message();
		$params = [
			'restaurants' => $details,
			'menus' => $menus,
			'mailId' => $email->id,
			'unsubscribe' => DOMAIN . '/Homepage/unsubscribe?emailId=' . $email->id];
		$Message->
				addTo($email->e_mail)->
				setHtmlBody($Latte->renderToString(__DIR__ . '/templates/Cli/mail.latte', $params))->
				setSubject('Jídelníčky');
		var_dump($Message);
		return $Message;
	}


}
