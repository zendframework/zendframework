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

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\HelperBroker as ViewHelperBroker;
use Zend\View\HelperLoader as ViewHelperLoader;
use Zend\View\Renderer\PhpRenderer as ViewPhpRenderer;
use Zend\View\Resolver as ViewResolver;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\View;

/**
 * Prepares the view layer
 *
 * Instantiates and configures all classes related to the view layer, including 
 * the renderer (and its associated resolver(s) and helper broker), the view 
 * object (and its associated rendering strategies), and the various MVC
 * strategies and listeners.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ViewManager implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var object application configuration service
     */
    protected $config;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $services;

    /**@+
     * Various properties representing strategies and objects instantiated and
     * configured by the view manager
     */
    protected $exceptionStrategy;
    protected $helperBroker;
    protected $helperLoader;
    protected $mvcRenderingStrategy;
    protected $renderer;
    protected $rendererStrategy;
    protected $resolver;
    protected $routeNotFoundStrategy;
    protected $view;
    protected $viewModel;
    /**@-*/

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('bootstrap', array($this, 'onBootstrap'), 10000);
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
     * Prepares the view layer
     * 
     * @param  ApplicationInterface $application 
     * @return void
     */
    public function onBootstrap($event)
    {
        $application  = $event->getApplication();
        $services     = $application->getServiceManager();
        $config       = $services->get('Configuration');
        $events       = $application->events();
        $sharedEvents = $events->getSharedManager();

        $this->config   = $config;
        $this->services = $services;
        $this->event    = $event;

        $routeNotFoundStrategy   = $this->getRouteNotFoundStrategy();
        $exceptionStrategy       = $this->getExceptionStrategy();
        $mvcRenderingStrategy    = $this->getMvcRenderingStrategy();
        $createViewModelListener = new CreateViewModelListener();
        $injectTemplateListener  = new InjectTemplateListener();
        $injectViewModelListener = new InjectViewModelListener();

        $events->attach($routeNotFoundStrategy);
        $events->attach($exceptionStrategy);
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($injectViewModelListener, 'injectViewModel'), -100);
        $events->attach($mvcRenderingStrategy);
        
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($createViewModelListener, 'createViewModelFromArray'), -80);
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($routeNotFoundStrategy, 'prepareNotFoundViewModel'), -90);
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($createViewModelListener, 'createViewModelFromNull'), -80);
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectTemplateListener, 'injectTemplate'), -90);
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectViewModelListener, 'injectViewModel'), -100);
    }

    /**
     * Instantiates and configures the renderer's helper loader
     * 
     * @return ViewHelperLoader
     */
    protected function getHelperLoader()
    {
        if ($this->helperLoader) {
            return $this->helperLoader;
        }

        $map = array();
        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['helper_map'])) {
            $map = $this->config['view_manager']['helper_map'];
        }
        $this->helperLoader = new ViewHelperLoader($map);
        return $this->helperLoader;
    }

    /**
     * Instantiates and configures the renderer's helper broker
     * 
     * @return ViewHelperBroker
     */
    protected function getHelperBroker()
    {
        if ($this->helperBroker) {
            return $this->helperBroker;
        }

        $this->helperBroker = new ViewHelperBroker();
        $this->helperBroker->setClassLoader($this->getHelperLoader());

        // Configure URL view helper with router
        $router = $this->services->get('Router');
        $url    = $this->helperBroker->load('url');
        $url->setRouter($router);

        // Configure basePath view helper with base path from configuration, if available
        $basePath = '/';
        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['base_path'])) {
            $basePath = $this->config['view_manager']['base_path'];
        }
        $basePathHelper = $this->helperBroker->load('basePath');
        $basePathHelper->setBasePath($basePath);

        // Configure doctype view helper with doctype from configuration, if available
        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['doctype'])) {
            $doctype = $this->helperBroker->load('doctype');
            $doctype->setDoctype($this->config['view_manager']['doctype']);
        }

        return $this->helperBroker;
    }

    /**
     * Instantiates and configures the renderer's resolver
     * 
     * @return ViewAggregateResolver
     */
    protected function getResolver()
    {
        if ($this->resolver) {
            return $this->resolver;
        }

        $map = array();
        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['template_map'])) {
            $map = $this->config['view_manager']['template_map'];
        }
        $templateMapResolver = new ViewResolver\TemplateMapResolver($map);

        $stack = array();
        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['template_path_stack'])) {
            $stack = $this->config['view_manager']['template_path_stack'];
        }
        $templatePathStack = new ViewResolver\TemplatePathStack($stack);

        $this->resolver = new ViewResolver\AggregateResolver();
        $this->resolver->attach($templateMapResolver);
        $this->resolver->attach($templatePathStack);

        $this->services->setService('ViewTemplateMapResolver', $templateMapResolver);
        $this->services->setService('ViewTemplatePathStack', $templatePathStack);
        $this->services->setService('ViewResolver', $this->resolver);

        return $this->resolver;
    }

    /**
     * Instantiates and configures the renderer
     * 
     * @return ViewPhpRenderer
     */
    protected function getRenderer()
    {
        if ($this->renderer) {
            return $this->renderer;
        }

        $this->renderer = new ViewPhpRenderer;
        $this->renderer->setBroker($this->getHelperBroker());
        $this->renderer->setResolver($this->getResolver());

        $model       = $this->getViewModel();
        $modelHelper = $this->renderer->plugin('view_model');
        $modelHelper->setRoot($model);

        $this->services->setService('ViewRenderer', $this->renderer);

        return $this->renderer;
    }

    /**
     * Instantiates and configures the renderer strategy for the view
     * 
     * @return PhpRendererStrategy
     */
    protected function getRendererStrategy()
    {
        if ($this->rendererStrategy) {
            return $this->rendererStrategy;
        }

        $this->rendererStrategy = new PhpRendererStrategy(
            $this->getRenderer()
        );
        return $this->rendererStrategy;
    }

    /**
     * Instantiates and configures the view
     * 
     * @return View
     */
    protected function getView()
    {
        if ($this->view) {
            return $this->view;
        }

        $this->view = new View();
        $this->view->setEventManager($this->services->get('EventManager'));
        $this->view->events()->attach($this->getRendererStrategy());

        $this->services->setService('View', $this->view);
        return $this->view;
    }

    /**
     * Retrieves the layout template name from the configuration
     * 
     * @return string
     */
    protected function getLayoutTemplate()
    {
        $layout = 'layout/layout';
        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['layout'])) {
            $layout = $this->config['view_manager']['layout'];
        }
        return $layout;
    }

    /**
     * Instantiates and configures the default MVC rendering strategy
     * 
     * @return DefaultRenderingStrategy
     */
    protected function getMvcRenderingStrategy()
    {
        if ($this->mvcRenderingStrategy) {
            return $this->mvcRenderingStrategy;
        }

        $this->mvcRenderingStrategy = new DefaultRenderingStrategy($this->getView());
        $this->mvcRenderingStrategy->setLayoutTemplate($this->getLayoutTemplate());
        return $this->mvcRenderingStrategy;
    }

    /**
     * Instantiates and configures the exception strategy
     * 
     * @return ExceptionStrategy
     */
    protected function getExceptionStrategy()
    {
        if ($this->exceptionStrategy) {
            return $this->exceptionStrategy;
        }

        $this->exceptionStrategy = new ExceptionStrategy();

        $displayExceptions = false;
        $exceptionTemplate = 'error';

        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['display_exceptions'])) {
            $displayExceptions = $this->config['view_manager']['display_exceptions'];
        }
        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['exception_template'])) {
            $exceptionTemplate = $this->config['view_manager']['exception_template'];
        }

        $this->exceptionStrategy->setDisplayExceptions($displayExceptions);
        $this->exceptionStrategy->setExceptionTemplate($exceptionTemplate);
        return $this->exceptionStrategy;
    }

    /**
     * Instantiates and configures the "route not found", or 404, strategy
     * 
     * @return RouteNotFoundStrategy
     */
    protected function getRouteNotFoundStrategy()
    {
        if ($this->routeNotFoundStrategy) {
            return $this->routeNotFoundStrategy;
        }

        $this->routeNotFoundStrategy = new RouteNotFoundStrategy();

        $displayNotFoundReason = false;
        $notFoundTemplate      = '404';

        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['display_not_found_reason'])) {
            $displayNotFoundReason = $this->config['view_manager']['display_not_found_reason'];
        }
        if (isset($this->config['view_manager']) && isset($this->config['view_manager']['not_found_template'])) {
            $notFoundTemplate = $this->config['view_manager']['not_found_template'];
        }

        $this->routeNotFoundStrategy->setDisplayNotFoundReason($displayNotFoundReason);
        $this->routeNotFoundStrategy->setNotFoundTemplate($notFoundTemplate);
        return $this->routeNotFoundStrategy;
    }

    /**
     * Configures the MvcEvent view model to ensure it has the template injected
     * 
     * @return \Zend\Mvc\View\Model\ModelInterface
     */
    protected function getViewModel()
    {
        if ($this->viewModel) {
            return $this->viewModel;
        }

        $this->viewModel = $model = $this->event->getViewModel();
        $model->setTemplate($this->getLayoutTemplate());
        return $this->viewModel;
    }
}
