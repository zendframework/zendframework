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
    Zend\Http\Request as HttpRequest,
    Zend\Mvc\MvcEvent,
    Zend\View\Model as ViewModel,
    Zend\View\PhpRenderer,
    Zend\View\Renderer\FeedRenderer,
    Zend\View\Renderer\JsonRenderer,
    Zend\View\View,
    Zend\View\ViewEvent;

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
     * @var bool
     */
    protected $useDefaultRenderingStrategy = true;

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
     * Set flag indicating whether or not to use default rendering strategy
     * 
     * @param  bool $flag 
     * @return DefaultRenderingStrategy
     */
    public function setUseDefaultRenderingStrategy($flag)
    {
        $this->useDefaultRenderingStrategy = (bool) $flag;
        return $this;
    }

    /**
     * Use the default rendering strategy?
     * 
     * @return bool
     */
    public function useDefaultRenderingStrategy()
    {
        return $this->useDefaultRenderingStrategy;
    }

    /**
     * Attach the aggregate to the specified event manager
     * 
     * @param  EventCollection $events 
     * @return void
     */
    public function attach(EventCollection $events)
    {
        $this->listeners[] = $events->attach('dispatch', array($this, 'render'), -10000);
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
    public function render($e)
    {
        if (!$e instanceof MvcEvent) {
            // don't know what to do if we don't have MVC-related params
            return;
        }

        // Martial arguments
        $request   = $e->getRequest();
        $response  = $e->getResponse();
        $viewModel = $e->getResult();
        if (!$viewModel instanceof ViewModel) {
            if (!is_array($viewModel) && !$viewModel instanceof Traversable) {
                // Don't know how to handle this
                return;
            }

            $viewModel = new ViewModel\ViewModel($viewModel);
        }

        // Attach default strategies
        if ($this->useDefaultRenderingStrategy()) {
            $this->attachDefaultStrategies();
        }

        $view = $this->view;
        $view->setRequest($request);
        $view->setResponse($response);

        $view->render($viewModel);
        return $response;
    }

    /**
     * Select a renderer by context
     *
     * If a specific view model type is selected, attempt to map it to a specific
     * renderer.
     *
     * Otherwise, check the Accept headers, and use those to determine appropriate
     * renderer.
     * 
     * @param  ViewEvent $e 
     * @return null|\Zend\View\Renderer
     */
    public function selectRendererByContext(ViewEvent $e)
    {
        $viewModel = $e->getResult();

        // Test for specific result types
        switch (true) {
            case ($viewModel instanceof Model\JsonModel):
                if ($this->view->hasRenderer('Zend\View\Renderer\JsonRenderer')) {
                    return $this->view->getRenderer('Zend\View\Renderer\JsonRenderer');
                }
                break;
            case ($viewModel instanceof Model\FeedModel):
                if ($this->view->hasRenderer('Zend\View\Renderer\FeedRenderer')) {
                    return $this->view->getRenderer('Zend\View\Renderer\FeedRenderer');
                }
                break;
            default:
                break;
        }

        // Test against Accept header
        if ($request instanceof HttpRequest) {
            if ($request->headers()->has('Accept')) {
                $accept = $request->get('Accept');
                foreach ($accept->getPrioritized() as $mediaType) {
                    switch (true) {
                        case (0 === strpos($mediaType, 'application/json')):
                            // JSON
                            if ($this->view->hasRenderer('Zend\View\Renderer\JsonRenderer')) {
                                return $this->view->getRenderer('Zend\View\Renderer\JsonRenderer');
                            }
                            break;
                        case (0 === strpos($mediaType, 'application/rss+xml')):
                        case (0 === strpos($mediaType, 'application/atom+xml')):
                            // RSS or Atom feed
                            if ($this->view->hasRenderer('Zend\View\Renderer\FeedRenderer')) {
                                return $this->view->getRenderer('Zend\View\Renderer\FeedRenderer');
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        // Last straw: use the PhpRenderer
        if (!$this->view->hasRenderer('Zend\View\PhpRenderer')) {
            $this->view->addRenderer(new PhpRenderer);
        }
        return $this->view->getRenderer('Zend\View\PhpRenderer');
    }

    /**
     * Populate the response object from the View
     *
     * Populates the content of the response object from the view rendering
     * results. Additionally, based on the renderer type, a Content-Type may be 
     * set.
     * 
     * @param  ViewEvent $e 
     * @return void
     */
    public function populateResponse(ViewEvent $e)
    {
        $result   = $e->getResult();
        $response = $e->getResponse();

        // Set content
        $response->setContent($result);

        // Attempt to set content-type header
        $renderer = $e->getTarget()->getRenderer();
        switch (true) {
            case ($renderer instanceof JsonRenderer):
                $response->headers()->addHeaderLine('content-type', 'application/json');
                break;
            case ($renderer instanceof FeedRenderer):
                $type = $renderer->getFeedType();
                $type = ('rss' == $type)
                      ? 'application/rss+xml'
                      : 'application/atom+xml';
                $response->headers()->addHeaderLine('content-type', $type);
                break;
            default:
                break;
        }
    }

    /**
     * Attach the default strategies
     *
     * Also ensures that we have renderers for the default strategies.
     * 
     * @return void
     */
    protected function attachDefaultStrategies()
    {
        if (!$this->view->hasRenderer('Zend\View\Renderer\FeedRenderer')) {
            $this->view->addRenderer(new FeedRenderer);
        }
        if (!$this->view->hasRenderer('Zend\View\Renderer\JsonRenderer')) {
            $this->view->addRenderer(new JsonRenderer);
        }
        if (!$this->view->hasRenderer('Zend\View\PhpRenderer')) {
            $this->view->addRenderer(new PhpRenderer);
        }

        $this->view->addRenderingStrategy(array($this, 'selectRendererByContext'), -100);
        $this->view->addResponseStrategy(array($this, 'populateResponse'), -100);
    }
}
