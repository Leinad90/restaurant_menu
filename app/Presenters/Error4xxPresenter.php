<?php
/**
 * E4XX pages
 * php version 8.0.11
 *
 * @category Index
 * @package  Restaurant Menu
 * @author   Daniel Hejduk <daniel.hejduk at gmail.com>
 * @licence  None
 * @link     https://github.com/Leinad90/restaurant_menu
 */

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use \Nette\Application\BadRequestException;

/**
 * Presenter to E4xx
 */
final class Error4xxPresenter extends Nette\Application\UI\Presenter
{
    /**
      @inheritDoc
     */
    public function startup(): void
    {
        parent::startup();
        if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
            $this->error();
        }
    }

    /**
     * Render error page
     *
     * @param  BadRequestException $exception
     * @return void
     */
    public function renderDefault(BadRequestException $exception): void
    {
        \Tracy\Debugger::log($exception);
        // load template 403.latte or 404.latte or ... 4xx.latte
        $file = __DIR__ . "/templates/Error/{$exception->getCode()}.latte";
        $this->template->setFile(
            is_file($file) ? $file : __DIR__ . '/templates/Error/4xx.latte'
        );
    }
}
