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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Navigation;

use Traversable;

/**
 * A simple container class for {@link Zend_Navigation_Page} pages
 *
 * @category  Zend
 * @package   Zend_Navigation
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Navigation extends Container
{
    /**
     * Creates a new navigation container
     *
     * @param  array|Traversable $pages    [optional] pages to add
     * @throws Exception\InvalidArgumentException  if $pages is invalid
     */
    public function __construct($pages = null)
    {
        if ($pages && (!is_array($pages) && !$pages instanceof Traversable)) {
            throw new Exception\InvalidArgumentException(
                'Invalid argument: $pages must be an array, an '
                . 'instance of Traversable, or null'
            );
        }

        if ($pages) {
            $this->addPages($pages);
        }
    }
}
