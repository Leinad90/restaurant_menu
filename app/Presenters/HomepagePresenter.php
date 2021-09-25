<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
    public function __construct(public \App\Services\RestaurantApi $restaurantApi)
	{
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
}
