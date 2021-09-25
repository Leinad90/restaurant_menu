<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
    public function __construct(public \App\Services\RestaurantApi $restaurantApi) {
		parent::__construct();
    }
	
	public function renderDefault() {
		$this->template->restaurants = $this->restaurantApi->getList();
	}
}
