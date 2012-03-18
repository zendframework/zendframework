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
 * @package    Zend_View
 * @subpackage Strategy
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Strategy;

use Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate,
    Zend\Console\Request as ConsoleRequest,
    Zend\Console\Response as ConsoleResponse,
    Zend\View\Model,
    Zend\View\Renderer\ConsoleRenderer,
    Zend\Console\Adapter as Console,
    Zend\View\ViewEvent;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Strategy
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ConsoleStrategy implements ListenerAggregate
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var ConsoleRenderer
     */
    protected $renderer;

    /**
     * @var Console
     */
    protected $console;

    /**
     * Constructor
     * 
     * @param  ConsoleRenderer $renderer 
     * @return void
     */
    public function __construct(ConsoleRenderer $renderer, Console $console)
    {
        $this->renderer = $renderer;
        $this->console = $console;
    }

    /**
     * Retrieve the composed renderer
     * 
     * @return ConsoleRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Attach the aggregate to the specified event manager
     * 
     * @param  EventCollection $events 
     * @param  int $priority
     * @return void
     */
    public function attach(EventCollection $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('renderer', array($this, 'selectRenderer'), $priority);
        $this->listeners[] = $events->attach('response', array($this, 'outputToConsole'), $priority);
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
     * Select the ConsoleRenderer; typically, this will be registered last or at 
     * low priority.
     * 
     * @param  ViewEvent $e 
     * @return ConsoleRenderer
     */
    public function selectRenderer(ViewEvent $e)
    {
        return $this->renderer;
    }

    /**
     * Populate the response object from the View
     *
     * Populates the content of the response object from the view rendering
     * results. 
     * 
     * @param  ViewEvent $e 
     * @return void
     */
    public function outputToConsole(ViewEvent $e)
    {
        $renderer = $e->getRenderer();
        if ($renderer !== $this->renderer) {
            return;
        }

        $result   = $e->getResult();
        $response = $e->getResponse();
        $response->setContent($result);

        $this->console->write($result);
    }
}
