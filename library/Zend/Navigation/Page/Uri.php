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
 * @package    Zend_Navigation
 * @subpackage Page
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Navigation\Page;

use Zend\Navigation\AbstractPage,
    Zend\Navigation\Exception\InvalidArgumentException;

/**
 * Represents a page that is defined by specifying a URI
 *
 * @uses       \Zend\Navigation\Exception
 * @uses       \Zend\Navigation\Page\Page
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage Page
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Uri extends AbstractPage
{
    /**
     * Page URI
     *
     * @var string|null
     */
    protected $_uri = null;

    /**
     * Sets page URI
     *
     * @param  string $uri                page URI, must a string or null
     * @return \Zend\Navigation\Page\Uri   fluent interface, returns self
     * @throws \Zend\Navigation\InvalidArgumentException  if $uri is invalid
     */
    public function setUri($uri)
    {
        if (null !== $uri && !is_string($uri)) {
            throw new InvalidArgumentException(
                    'Invalid argument: $uri must be a string or null');
        }

        $this->_uri = $uri;
        return $this;
    }

    /**
     * Returns URI
     *
     * @return string
     */
    public function getUri()
    {
        return $this->_uri;
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

    // Public methods:

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
                'uri' => $this->getUri()
            ));
    }
}
