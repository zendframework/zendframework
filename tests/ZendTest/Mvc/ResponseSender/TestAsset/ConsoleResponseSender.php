<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\ResponseSender\TestAsset;

use Zend\Console\Response;
use Zend\Mvc\ResponseSender\ConsoleResponseSender as BaseConsoleResponseSender;
use Zend\Mvc\ResponseSender\SendResponseEvent;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class ConsoleResponseSender extends BaseConsoleResponseSender
{
    /**
     * Send the response
     *
     * This method is overridden, it's purpose is to disable the exit call and instead
     * just return the error level for unit testing
     *
     * @param SendResponseEvent $event
     * @return int
     */
    public function __invoke(SendResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response instanceof Response) {
            $this->sendContent($event);
            $errorLevel = (int) $response->getMetadata('errorLevel',0);
            $event->stopPropagation(true);
            return $errorLevel;
        }
    }
}
