<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Session;

/**
 * Session storage container for PHP 5.3.3 and less
 */
class Container extends AbstractContainer
{
    /**
     * Exchange the current array with another array or object.
     *
     * @param  array|object $input
     * @return array        Returns the old array
     * @see ArrayObject::exchangeArray()
     */
    public function exchangeArray($input)
    {
        return parent::exchangeArrayCompat($input);
    }
}
