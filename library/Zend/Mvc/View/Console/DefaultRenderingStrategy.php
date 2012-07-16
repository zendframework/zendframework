<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\View\Console;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Console\Response as ConsoleResponse;
use Zend\Console\Request as ConsoleRequest;
use Zend\View\Model\ConsoleModel as ConsoleViewModel;
use Zend\View\Model\ModelInterface as ViewModel;
use Zend\View\View;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 */
class DefaultRenderingStrategy implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Set view
     *
     * @param  View $view
     * @return DefaultRenderingStrategy
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'render'), -10000);
    }

    /**
     * Detach aggregate listeners from the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
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
     * @return Response
     */
    public function render(MvcEvent $e)
    {
        $result = $e->getResult();
        if ($result instanceof Response) {
            return $result;
        }

        // martial arguments
        $response  = $e->getResponse();
        $viewModel = $e->getViewModel();

        // collect results from child models
        $responseText = '';
        if($result->hasChildren()){
            foreach($result->getChildren() as $child){
                /* @var $child ViewModel */
                $responseText .= $child->getVariable(ConsoleViewModel::RESULT);
            }
        }

        // fetch result from primary model
        $responseText .= $result->getVariable(ConsoleViewModel::RESULT);

        // append console response to response object
        $response->setContent(
            $response->getContent() . $responseText
        );

        // pass on console-specific options
        if(
            $response  instanceof ConsoleResponse &&
            $result    instanceof ConsoleViewModel
        ){
            /* @var $response ConsoleResponse */
            /* @var $result ConsoleViewModel */
            $errorLevel = $result->getErrorLevel();
            $response->setErrorLevel($errorLevel);
        }

        return $response;
    }
}
