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
    Zend\Http\Response as HttpResponse,
    Zend\Mvc\Application,
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
     * @var string
     */
    protected $defaultLayout = 'layout';

    /**
     * @var bool
     */
    protected $displayExceptions = false;

    /**
     * @var bool
     */
    protected $enableLayoutForErrors = true;

    /**
     * View models incapable of layouts
     * @var array
     */
    protected $layoutIncapableModels = array(
        'Zend\View\Model\FeedModel',
        'Zend\View\Model\JsonModel',
    );

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var bool
     */
    protected $useDefaultRenderingStrategy = true;

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
     * Set value for default layout
     *
     * @param  string $defaultLayout
     * @return DefaultRenderingStrategy
     */
    public function setDefaultLayout($defaultLayout)
    {
        $this->defaultLayout = (string) $defaultLayout;
        return $this;
    }
    
    /**
     * Get default layout
     *
     * @return string
     */
    public function getDefaultLayout()
    {
        return $this->defaultLayout;
    }

    /**
     * Flag: display exceptions in error pages?
     * 
     * @param  bool $flag 
     * @return DefaultRenderingStrategy
     */
    public function setDisplayExceptions($displayExceptions)
    {
        $this->displayExceptions = (bool) $displayExceptions;
        return $this;
    }

    /**
     * Should we display exceptions in error pages?
     * 
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->displayExceptions;
    }

    /**
     * Set flag indicating whether or not to enable layouts for errors
     *
     * @param  bool $enableLayoutForErrors
     * @return DefaultRenderingStrategy
     */
    public function setEnableLayoutForErrors($enableLayoutForErrors)
    {
        $this->enableLayoutForErrors = (bool) $enableLayoutForErrors;
        return $this;
    }
    
    /**
     * Are layouts for errors enabled?
     *
     * @return bool
     */
    public function enableLayoutForErrors()
    {
        return $this->enableLayoutForErrors;
    }

    /**
     * Set value for modelTypes
     *
     * @param  array $modelTypes
     * @return DefaultRenderingStrategy
     */
    public function setLayoutIncapableModels(array $modelTypes)
    {
        $this->layoutIncapableModels = $modelTypes;
        return $this;
    }
    
    /**
     * Get list of models incapable of layouts
     *
     * @return array
     */
    public function getLayoutIncapableModels()
    {
        return $this->layoutIncapableModels;
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
        $this->listeners[] = $events->attach('dispatch', array($this, 'render404'), -1000);
        $this->listeners[] = $events->attach('dispatch', array($this, 'render'), -10000);
        $this->listeners[] = $events->attach('dispatch.error', array($this, 'renderError'));
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
     * Create an error view model and render it
     * 
     * @param  MvcEvent $e 
     * @return HttpResponse
     */
    public function renderError(MvcEvent $e)
    {
        $error    = $e->getError();
        $response = $e->getResponse();
        if (!$response) {
            $response = new HttpResponse();
            $e->setResponse($response);
        }

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
                $vars = array(
                    'message' => 'Page not found.',
                );
                $options = array(
                    'template' => 'pages/404',
                );
                $response->setStatusCode(404);
                break;

            case Application::ERROR_EXCEPTION:
            default:
                $exception = $e->getParam('exception');
                $vars = array(
                    'message'            => 'An error occurred during execution; please try again later.',
                    'exception'          => $e->getParam('exception'),
                    'display_exceptions' => $this->displayExceptions(),
                );
                $options = array(
                    'template' => 'error',
                );
                $response->setStatusCode(500);
                break;
        }
        $options['enable_layout'] = $this->enableLayoutForErrors();

        $model = new ViewModel\ViewModel($vars, $options);
        $e->setResult($model);
        return $this->render($e);
    }

    /**
     * Create and return a 404 page
     * 
     * @param  MvcEvent $e 
     * @return null|HttpResponse
     */
    public function render404(MvcEvent $e)
    {
        $vars = $e->getResult();
        if ($vars instanceof Response) {
            return;
        }

        $response = $e->getResponse();
        if ($response->getStatusCode() != 404) {
            // Only handle 404's
            return;
        }

        $model = new ViewModel\ViewModel(
            array('message' => 'Page not found.'),
            array(
                'template'      => 'pages/404',
                'enable_layout' => $this->enableLayoutForErrors(),
            )
        );
        $e->setResult($model);
        return $this->render($e);
    }


    /**
     * Select layout
     *
     * Listens on 'renderer' event. Instead of returning a renderer, it simply
     * checks the following:
     *
     * - is the view model in a blacklist? if so, do nothing
     * - is it an XHR request? if so, do nothing
     * - if enable_layout was not set as a renderer option, do nothing
     * - if enable_layout was set as a renderer option, but set to a boolean
     *   false, do nothing
     * - if the PhpRenderer is not available, do nothing.
     *
     * Finally, if the conditions above are not met, it adds the layout to the
     * stack. The assumption is that the view script rendered will capture
     * content into placeholders that the layout viewscript will retrieve and
     * render.
     * 
     * @param  ViewEvent $e 
     * @return void
     */
    public function selectLayout(ViewEvent $e)
    {
        $model = $e->getModel();
        if (!$model instanceof ViewModel
            || in_array(get_class($model), $this->getLayoutIncapableModels())
        ) {
            return;
        }

        $request   = $e->getRequest();
        if ($request instanceof HttpRequest) {
            $headers = $request->headers();
            if ($headers->has('x-requested-with')) {
                $header = $headers->get('x-requested-with');
                if (strtolower($header->getFieldValue()) == 'xmlhttprequest') {
                    // XHR request; ignore
                    return;
                }
            }
        }

        $options = $model->getOptions();
        if (array_key_exists('enable_layout', $options) && !$options['enable_layout']) {
            return;
        }

        $view = $e->getTarget();
        if (!$view->hasRenderer('Zend\View\PhpRenderer')) {
            return;
        }
        $renderer = $view->getRenderer('Zend\View\PhpRenderer');

        $layout = $this->getDefaultLayout();
        if (isset($options['layout'])) {
            $layout = $options['layout'];
        }
        $renderer->enqueue($layout);
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
        $viewModel = $e->getModel();

        // Test for specific result types
        switch (true) {
            case ($viewModel instanceof ViewModel\JsonModel):
                if ($this->view->hasRenderer('Zend\View\Renderer\JsonRenderer')) {
                    return $this->view->getRenderer('Zend\View\Renderer\JsonRenderer');
                }
                break;
            case ($viewModel instanceof ViewModel\FeedModel):
                if ($this->view->hasRenderer('Zend\View\Renderer\FeedRenderer')) {
                    return $this->view->getRenderer('Zend\View\Renderer\FeedRenderer');
                }
                break;
            default:
                break;
        }

        // Test against Accept header
        $request = $e->getRequest();
        if ($request instanceof HttpRequest) {
            $headers = $request->headers();
            if ($headers->has('Accept')) {
                $accept = $headers->get('Accept');
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
        $renderer = $e->getRenderer();

        // Set content
        if (empty($result) && $renderer instanceof PhpRenderer) {
            $placeholders = $renderer->plugin('placeholder');
            $registry     = $placeholders->getRegistry();
            if ($registry->containerExists('article')) {
                $result = (string) $registry->getContainer('article');
            } elseif ($registry->containerExists('content')) {
                $result = (string) $registry->getContainer('content');
            }
        }
        $response->setContent($result);

        // Attempt to set content-type header
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

        $this->view->addRenderingStrategy(array($this, 'selectLayout'), -99);
        $this->view->addRenderingStrategy(array($this, 'selectRendererByContext'), -100);
        $this->view->addResponseStrategy(array($this, 'populateResponse'), -100);
    }
}
