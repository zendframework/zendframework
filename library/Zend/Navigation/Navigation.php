<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Navigation
 */

namespace Zend\Navigation;

use Traversable;

/**
 * A simple container class for {@link Zend_Navigation_Page} pages
 *
 * @category  Zend
 * @package   Zend_Navigation
 */
class Navigation extends AbstractContainer
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
