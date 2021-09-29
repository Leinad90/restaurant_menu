<?php
/**
 * Base index file starting aplication
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
use Nette\Application\Responses;
use Nette\Http;
use Tracy\ILogger;

/**
 * Fallback presenter to show errors
 *
 * @category Index
 * @package  Restaurant_Menu
 * @author   Daniel Hejduk <daniel.hejduk@gmail.com>
 * @license  None https://en.wikipedia.org/wiki/Empty_set
 * @link     https://github.com/Leinad90/restaurant_menu
 */
final class ErrorPresenter implements Nette\Application\IPresenter
{
    use Nette\SmartObject;

    /**
     * Base constuctor
     *
     * @param ILogger $logger Where to log exceptions
     */
    public function __construct(private ILogger $logger)
    {
    }


    /**
     * Show error pagge
     *
     * @param Nette\Application\Request $request HTTP Request
     * 
     * @return Nette\Application\Response Response
     */
    public function run(
        Nette\Application\Request $request
    ): Nette\Application\Response {
        $exception = $request->getParameter('exception');

        if ($exception instanceof Nette\Application\BadRequestException) {
            [$module, , $sep] = Nette\Application\Helpers::splitName(
                $request->getPresenterName()
            );
            return new Responses\ForwardResponse(
                $request->setPresenterName($module . $sep . 'Error4xx')
            );
        }

        $this->logger->log($exception, ILogger::EXCEPTION);
        return new Responses\CallbackResponse(
            function (
                Http\IRequest $httpRequest,
                Http\IResponse $httpResponse
            ): void {
                if (preg_match(
                    '#^text/html(?:;|$)#',
                    (string) $httpResponse->getHeader('Content-Type')
                )
                ) {
                    include __DIR__ . '/templates/Error/500.phtml';
                }
            }
        );
    }
}
