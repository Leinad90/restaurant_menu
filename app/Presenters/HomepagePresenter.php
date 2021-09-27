<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
			private \App\Services\RestaurantApi $restaurantApi,
			private \App\Services\EmailModel $emailModel,
			private \App\Services\RestaurantModel $restaurantModel
	) {
		parent::__construct();
    }
	
	public function renderDefault()
	{
		$this->template->restaurants = $this->restaurantApi->getList();
	}
	
	public function renderDetail(int $restaurantId)
	{
		$this->template->restaurant = $this->restaurantApi->getDetail($restaurantId);
		$this->template->menu = $this->restaurantApi->getMenu($restaurantId);
	}
	
	public function renderRegister()
	{
		$this->updateRestaurantList();
	}


	public function createComponentForm(): Form
	{
		$form = new Form();
		$form->addEmail('email', 'E-mail')->setRequired('Zadejte email');
		$form->addCheckboxList('restaurants', 'Restaurace', $this->restaurantApi->getListForCheckbox())->setRequired('Vyberte aspoň jednu restauraci');
		$form->addSubmit('save', 'registrovat');
		$form->onSuccess [] = [$this,'registerMail'];
		return $form;
	}
	
	public function registerMail(Form $form, $formData)
	{
		$this->emailModel->begin();
		try {
			$emailId = $this->emailModel->insert($formData->email);
		} catch (\App\Services\EmailModelException $e) {
			$this->flashMessage('Zasílání nelze registrovat. ');
			if($e->getCode()== \App\Services\EmailModelException::ERROR_ADDRESS_EXISTS) {
				$form['email']->addError('Adresa je již registrována. ');
			}
			$this->emailModel->rollback();
			return false;
		}
		$this->emailModel->insertRestaurants($emailId, $formData->restaurants);
		$this->emailModel->commit();
		$this->flashMessage('Zasílání úspěšně registrováno');
		$this->redirect('default');
	}
	
	private function updateRestaurantList()
	{
		$restaurants = $this->restaurantApi->getList();
		foreach ($restaurants as $restaurant) {
			$this->restaurantModel->insertUpdate($restaurant->name, $restaurant->id);
		}	
	}
}
