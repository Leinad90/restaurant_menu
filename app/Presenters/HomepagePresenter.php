<?php
/**
 * Preseter for WUI
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */


declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use \App\Services\EmailModelException; 

/**
 * Base presenter for WUI
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */
class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /**
     * Constructs presenter
     *
     * @param \App\Services\RestaurantApi   $restaurantApi   Service for downloading
     * @param \App\Services\EmailModel      $emailModel      Database Service
     * @param \App\Services\RestaurantModel $restaurantModel Database servicr
     */
    public function __construct(
        private \App\Services\RestaurantApi $restaurantApi,
        private \App\Services\EmailModel $emailModel,
        private \App\Services\RestaurantModel $restaurantModel
    ) {
        parent::__construct();
    }
    
    /**
     * Render default page - list of restaurants
     * 
     * @return void
     */
    public function renderDefault() : void
    {
        try {
            $this->template->restaurants = $this->restaurantApi->getList();
        } catch (\App\Services\DownloaderException $e) {
            $this->flashMessage($e->getMessage());
            $this->error(
                $e->getMessage(),
                Nette\Http\IResponse::S503_SERVICE_UNAVAILABLE
            );
        }
    }
    
    /**
     * Render page with info about specific restaurant
     *
     * @param int $restaurantId ID of selected restaurant
     * 
     * @return void
     */
    public function renderDetail(int $restaurantId)
    {
        try {
            $this->template->restaurant
                = $this->restaurantApi->getDetail($restaurantId);
            $this->template->menu = $this->restaurantApi->getMenu($restaurantId);
        } catch (\App\Services\DownloaderException $e) {
            $this->flashMessage($e->getMessage());
            $this->error(
                $e->getMessage(),
                Nette\Http\IResponse::S503_SERVICE_UNAVAILABLE
            );
        }
    }
    
    /**
     * Render page for subscribe
     *
     * @return void
     */
    public function renderRegister()
    {
        try {
            $this->_updateRestaurantList();
        } catch (\App\Services\RestaurantApiException $e) {
            $this->flashMessage(
                "Seznam restaurací není aktuální, zkuste to později"
            );
        }
    }
    
    /**
     * Render page for unsubscribe
     *
     * @param int $mailId ID of mail address
     * 
     * @return void 
     */
    public function renderUnsubscribe(int $mailId): void
    {
        $this->emailModel->unsubcribe($mailId);
        $this->flashMessage('Emailing odhlášen');
        $this->redirect('default');
    }

    /**
     * Create subsriction form
     *
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->addEmail('email', 'E-mail')->setRequired('Zadejte email');
        $form->addCheckboxList(
            'restaurants',
            'Restaurace',
            $this->restaurantApi->getListForCheckbox()
        )->setRequired('Vyberte aspoň jednu restauraci');
        $form->addSubmit('save', 'Přihlásit');
        $form->onSuccess [] = [$this,'registerMail'];
        return $form;
    }
    
    /**
     * Register mail subsribtion
     *
     * @param Form $form     Form as from createdcomponent
     * @param $formData Filled data
     * 
     * @return boolean is registered?
     */
    public function registerMail(Form $form, $formData)
    {
        $this->emailModel->begin();
        try {
            $emailId = $this->emailModel->insert($formData->email);
        } catch (EmailModelException $e) {
            $this->flashMessage('Zasílání nelze registrovat. ');
            if ($e->getCode() === EmailModelException::ERROR_ADDRESS_EXISTS) {
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
    
    /**
     * Updates database of restaurants to actual info in API
     *
     * @return void
     * @throws App\Services\DownloaderException
     */
    private function _updateRestaurantList(): void
    {
        $restaurants = $this->restaurantApi->getList();
        foreach ($restaurants as $restaurant) {
            $this->restaurantModel->insertUpdate($restaurant->name, $restaurant->id);
        }    
    }
}
