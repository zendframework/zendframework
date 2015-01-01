<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Navigation\Page;

use Zend\Http\Request;
use Zend\Navigation\Exception;

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
     * Request object used to determine uri path
     *
     * @var string
     */
    protected $request;

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

    /**
     * Returns whether page should be considered active or not
     *
     * This method will compare the page properties against the request uri.
     *
     * @param bool $recursive
     *            [optional] whether page should be considered
     *            active if any child pages are active. Default is
     *            false.
     * @return bool whether page should be considered active or not
     */
    public function isActive($recursive = false)
    {
        if (!$this->active) {
            if ($this->getRequest() instanceof Request) {
                if ($this->getRequest()->getUri()->getPath() == $this->getUri()) {
                    $this->active = true;
                    return true;
                }
            }
        }

        return parent::isActive($recursive);
    }

    /**
     * Get the request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets request for assembling URLs
     *
     * @param Request $request
     * @return self Fluent interface, returns self
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
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
