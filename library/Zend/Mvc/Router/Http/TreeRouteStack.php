<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Router\Http;

use Traversable;
use Zend\I18n\Translator;
use Zend\Mvc\Router\Exception;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Uri\Http as HttpUri;

/**
 * Tree search implementation.
 */
class TreeRouteStack extends SimpleRouteStack implements Translator\TranslatorAwareInterface
{
    /**
     * Base URL.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Request URI.
     *
     * @var HttpUri
     */
    protected $requestUri;

    /**
     * Translator used for translatable segments.
     *
     * @var Translator\Translator
     */
    protected $translator;

    /**
     * Whether the translator is enabled.
     *
     * @var boolean
     */
    protected $translatorEnabled = true;

    /**
     * Translator text domain to use.
     *
     * @var string
     */
    protected $translatorTextDomain = 'default';

    /**
     * init(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::init()
     */
    protected function init()
    {
        $routes = $this->routePluginManager;
        foreach (array(
                'hostname' => __NAMESPACE__ . '\Hostname',
                'literal'  => __NAMESPACE__ . '\Literal',
                'part'     => __NAMESPACE__ . '\Part',
                'regex'    => __NAMESPACE__ . '\Regex',
                'scheme'   => __NAMESPACE__ . '\Scheme',
                'segment'  => __NAMESPACE__ . '\Segment',
                'wildcard' => __NAMESPACE__ . '\Wildcard',
                'query'    => __NAMESPACE__ . '\Query',
                'method'   => __NAMESPACE__ . '\Method',
            ) as $name => $class
        ) {
            $routes->setInvokableClass($name, $class);
        };
    }

    /**
     * addRoute(): defined by RouteStackInterface interface.
     *
     * @see    RouteStackInterface::addRoute()
     * @param  string  $name
     * @param  mixed   $route
     * @param  integer $priority
     * @return TreeRouteStack
     */
    public function addRoute($name, $route, $priority = null)
    {
        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        return parent::addRoute($name, $route, $priority);
    }

    /**
     * routeFromArray(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::routeFromArray()
     * @param  array|Traversable $specs
     * @return RouteInterface
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    protected function routeFromArray($specs)
    {
        if ($specs instanceof Traversable) {
            $specs = ArrayUtils::iteratorToArray($specs);
        } elseif (!is_array($specs)) {
            throw new Exception\InvalidArgumentException('Route definition must be an array or Traversable object');
        }

        $route = parent::routeFromArray($specs);

        if (!$route instanceof RouteInterface) {
            throw new Exception\RuntimeException('Given route does not implement HTTP route interface');
        }

        if (isset($specs['child_routes'])) {
            $options = array(
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate']),
                'child_routes'  => $specs['child_routes'],
                'route_plugins' => $this->routePluginManager,
            );

            $priority = (isset($route->priority) ? $route->priority : null);

            $route = $this->routePluginManager->get('part', $options);
            $route->priority = $priority;
        }

        return $route;
    }

    /**
     * match(): defined by \Zend\Mvc\Router\RouteInterface
     *
     * @see    \Zend\Mvc\Router\RouteInterface::match()
     * @param  Request      $request
     * @param  integer|null $pathOffset
     * @param  array        $options
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset = null, array $options = array())
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        if ($this->baseUrl === null && method_exists($request, 'getBaseUrl')) {
            $this->setBaseUrl($request->getBaseUrl());
        }

        $uri           = $request->getUri();
        $baseUrlLength = strlen($this->baseUrl) ?: null;

        if ($pathOffset !== null) {
            $baseUrlLength += $pathOffset;
        }

        if ($this->requestUri === null) {
            $this->setRequestUri($uri);
        }

        if ($this->hasTranslator() && $this->isTranslatorEnabled() && !isset($options['translator'])) {
            $options['translator'] = $this->getTranslator();
        }

        if (!isset($options['text_domain'])) {
            $options['text_domain'] = $this->getTranslatorTextDomain();
        }

        if ($baseUrlLength !== null) {
            $pathLength = strlen($uri->getPath()) - $baseUrlLength;

            foreach ($this->routes as $name => $route) {
                if (($match = $route->match($request, $baseUrlLength, $options)) instanceof RouteMatch && $match->getLength() === $pathLength) {
                    $match->setMatchedRouteName($name);

                    foreach ($this->defaultParams as $paramName => $value) {
                        if ($match->getParam($paramName) === null) {
                            $match->setParam($paramName, $value);
                        }
                    }

                    return $match;
                }
            }
        } else {
            return parent::match($request);
        }

        return null;
    }

    /**
     * assemble(): defined by \Zend\Mvc\Router\RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function assemble(array $params = array(), array $options = array())
    {
        if (!isset($options['name'])) {
            throw new Exception\InvalidArgumentException('Missing "name" option');
        }

        $names = explode('/', $options['name'], 2);
        $route = $this->routes->get($names[0]);

        if (!$route) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $names[0]));
        }

        if (isset($names[1])) {
            $options['name'] = $names[1];
        } else {
            unset($options['name']);
        }

        if (isset($options['only_return_path']) && $options['only_return_path']) {
            return $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);
        }

        if (!isset($options['uri'])) {
            $uri = new HttpUri();

            if (isset($options['force_canonical']) && $options['force_canonical']) {
                if ($this->requestUri === null) {
                    throw new Exception\RuntimeException('Request URI has not been set');
                }

                $uri->setScheme($this->requestUri->getScheme())
                    ->setHost($this->requestUri->getHost())
                    ->setPort($this->requestUri->getPort());
            }

            $options['uri'] = $uri;
        } else {
            $uri = $options['uri'];
        }

        if ($this->hasTranslator() && $this->isTranslatorEnabled() && !isset($options['translator'])) {
            $options['translator'] = $this->getTranslator();
        }

        if (!isset($options['text_domain'])) {
            $options['text_domain'] = $this->getTranslatorTextDomain();
        }

        $path = $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);

        if (isset($options['query'])) {
            $uri->setQuery($options['query']);
        }

        if (isset($options['fragment'])) {
            $uri->setFragment($options['fragment']);
        }

        if ((isset($options['force_canonical']) && $options['force_canonical']) || $uri->getHost() !== null || $uri->getScheme() !== null) {
            if (($uri->getHost() === null || $uri->getScheme() === null) && $this->requestUri === null) {
                throw new Exception\RuntimeException('Request URI has not been set');
            }

            if ($uri->getHost() === null) {
                $uri->setHost($this->requestUri->getHost());
            }

            if ($uri->getScheme() === null) {
                $uri->setScheme($this->requestUri->getScheme());
            }

            return $uri->setPath($path)->normalize()->toString();
        } elseif (!$uri->isAbsolute() && $uri->isValidRelative()) {
            return $uri->setPath($path)->normalize()->toString();
        }

        return $path;
    }

    /**
     * Set the base URL.
     *
     * @param  string $baseUrl
     * @return self
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the request URI.
     *
     * @param  HttpUri $uri
     * @return TreeRouteStack
     */
    public function setRequestUri(HttpUri $uri)
    {
        $this->requestUri = $uri;
        return $this;
    }

    /**
     * Get the request URI.
     *
     * @return HttpUri
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * setTranslator(): defined by Translator\TranslatorAwareInterface.
     *
     * @see    Translator\TranslatorAwareInterface::setTranslator()
     * @param  Translator\Translator $translator
     * @param  string                $textDomain
     * @return TreeRouteStack
     */
    public function setTranslator(Translator\Translator $translator = null, $textDomain = null)
    {
        $this->translator = $translator;

        if ($textDomain !== null) {
            $this->setTranslatorTextDomain($textDomain);
        }

        return $this;
    }

    /**
     * getTranslator(): defined by Translator\TranslatorAwareInterface.
     *
     * @see    Translator\TranslatorAwareInterface::getTranslator()
     * @return Translator\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * hasTranslator(): defined by Translator\TranslatorAwareInterface.
     *
     * @see    Translator\TranslatorAwareInterface::hasTranslator()
     * @return boolean
     */
    public function hasTranslator()
    {
        return $this->translator !== null;
    }

    /**
     * setTranslatorEnabled(): defined by Translator\TranslatorAwareInterface.
     *
     * @see    Translator\TranslatorAwareInterface::setTranslatorEnabled()
     * @param  boolean $enabled
     * @return TreeRouteStack
     */
    public function setTranslatorEnabled($enabled = true)
    {
        $this->translatorEnabled = $enabled;
        return $this;
    }

    /**
     * isTranslatorEnabled(): defined by Translator\TranslatorAwareInterface.
     *
     * @see    Translator\TranslatorAwareInterface::isTranslatorEnabled()
     * @return boolean
     */
    public function isTranslatorEnabled()
    {
        return $this->translatorEnabled;
    }

    /**
     * setTranslatorTextDomain(): defined by Translator\TranslatorAwareInterface.
     *
     * @see    Translator\TranslatorAwareInterface::setTranslatorTextDomain()
     * @param string $textDomain
     * @return mixed
     */
    public function setTranslatorTextDomain($textDomain = 'default')
    {
        $this->translatorTextDomain = $textDomain;

        return $this;
    }

    /**
     * getTranslatorTextDomain(): defined by Translator\TranslatorAwareInterface.
     *
     * @see    Translator\TranslatorAwareInterface::getTranslatorTextDomain()
     * @return string
     */
    public function getTranslatorTextDomain()
    {
        return $this->translatorTextDomain;
    }
}
