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
 * @category  Zend
 * @package   Zend_Navigation
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Navigation;

/**
 * A simple container class for {@link Zend_Navigation_Page} pages
 *
 * @uses      \Zend\Navigation\Container
 * @uses      \Zend\Navigation\InvalidArgumentException
 * @category  Zend
 * @package   Zend_Navigation
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Navigation extends Container
{
    /**
     * Creates a new navigation container
     *
     * @param array|\Zend\Config\Config $pages    [optional] pages to add
     * @throws \Zend\Navigation\InvalidArgumentException  if $pages is invalid
     */
    public function __construct($pages = null)
    {
        if (is_array($pages) || $pages instanceof \Zend\Config\Config) {
            $this->addPages($pages);
        } elseif (null !== $pages) {
            throw new Exception\InvalidArgumentException(
                    'Invalid argument: $pages must be an array, an ' .
                    'instance of Zend_Config, or null');
        }
    }
}
