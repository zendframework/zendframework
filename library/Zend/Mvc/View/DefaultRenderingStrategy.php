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

namespace Zend\Mvc\View;

use Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate,
    Zend\Mvc\MvcEvent,
    Zend\View\Model as ViewModel,
    Zend\View\View;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DefaultRenderingStrategy implements ListenerAggregate
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var View
     */
    protected $view;

    /**
     * Set view
     * 
     * @param  View $view 
     * @return DefaultRenderingStrategy
     */
    public function __construct(View $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Attach the aggregate to the specified event manager
     * 
     * @param  EventCollection $events 
     * @return void
     */
    public function attach(EventCollection $events)
    {
        $this->listeners[] = $events->attach('render', array($this, 'render'), -10000);
    }

    /**
     * Detach aggregate listeners from the specified event manager
     * 
     * @param  EventCollection $events 
     * @return void
     */
    public function detach(EventCollection $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Render the view
     * 
     * @param  MvcEvent $e 
     * @return \Zend\Stdlib\ResponseDescription
     */
    public function render(MvcEvent $e)
    {
        // Martial arguments
        $request   = $e->getRequest();
        $response  = $e->getResponse();
        $viewModel = $e->getResult();
        if (!$viewModel instanceof ViewModel) {
            return;
        }

        $view = $this->view;
        $view->setRequest($request);
        $view->setResponse($response);
        $view->render($viewModel);

        return $response;
    }
}
