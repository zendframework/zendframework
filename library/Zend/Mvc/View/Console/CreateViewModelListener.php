<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\View\Console;

use Zend\EventManager\EventManagerInterface as Events;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Http\CreateViewModelListener as HttpCreateViewModelListener;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ViewModel;

class CreateViewModelListener extends HttpCreateViewModelListener implements ListenerAggregateInterface
{
    /**
     * Attach listeners
     *
     * @param  Events $events
     * @return void
     */
    public function attach(Events $events)
    {
        parent::attach($events);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'createViewModelFromString'), -80);
    }

    /**
     * Inspect the result, and cast it to a ViewModel if a string is detected
     *
     * @param MvcEvent $e
     * @return void
    */
    public function createViewModelFromString(MvcEvent $e)
    {
        $result = $e->getResult();
        if (!is_string($result)) {
            return;
        }

        $model = new ViewModel;
        $model->setVariable('result',$result);
        $e->setResult($model);
    }
}
