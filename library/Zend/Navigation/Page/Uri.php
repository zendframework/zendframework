<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Navigation\Page;

use Zend\Navigation\Exception;
use Zend\Mvc\Router\RouteStackInterface;

/**
 * Represents a page that is defined by specifying a URI
 */
class Uri extends AbstractPage
{
    /**
     * Page URI
     *
     * @var string|null
     */
    protected $uri = null;
    
    /**
     * RouteInterface used to determine request uri path
     *
     * @var string
     */
    protected $route;

    /**
     * Sets page URI
     *
     * @param  string $uri                page URI, must a string or null
     *
     * @return Uri   fluent interface, returns self
     * @throws Exception\InvalidArgumentException  if $uri is invalid
     */
    public function setUri($uri)
    {
        if (null !== $uri && !is_string($uri)) {
            throw new Exception\InvalidArgumentException(
                'Invalid argument: $uri must be a string or null'
            );
        }

        $this->uri = $uri;
        return $this;
    }

    /**
     * Returns URI
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns href for this page
     *
     * Includes the fragment identifier if it is set.
     *
     * @return string
     */
    public function getHref()
    {
        $uri = $this->getUri();

        $fragment = $this->getFragment();
        if (null !== $fragment) {
            if ('#' == substr($uri, -1)) {
                return $uri . $fragment;
            } else {
                return $uri . '#' . $fragment;
            }
        }

        return $uri;
    }
    
    public function isActive($recursive = false)
    {
        $uri = $this->getUri();
        
        if (!$this->active) {
            $router = $this->getRouter();
            if (!$router instanceof RouteStackInterface) {
                throw new Exception\DomainException(
                        __METHOD__
                        . ' cannot execute as no Zend\Mvc\Router\RouteStackInterface instance is composed'
                );
            }
            
            if ($router->getRequestUri()->getPath() == $uri) {
                $this->active = true;
                return true;
            }
        }
    
        return parent::isActive($recursive);
    }
    
    /**
     * Get the router.
     *
     * @return null|RouteStackInterface
     */
    public function getRouter()
    {
        return $this->router;
    }
    
    /**
     * Sets router for assembling URLs
     *
     * @see getHref()
     *
     * @param  RouteStackInterface $router Router
     * @return Mvc    fluent interface, returns self
     */
    public function setRouter(RouteStackInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Returns an array representation of the page
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            array(
                'uri' => $this->getUri(),
            )
        );
    }
    
}
