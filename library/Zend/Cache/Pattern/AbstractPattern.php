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
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Pattern;

use Traversable,
    Zend\Cache\Exception,
    Zend\Cache\Pattern;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractPattern implements Pattern
{
    /**
     * Constructor
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Set pattern options
     *
     * @param  array|Traversable $options
     * @return AbstractPattern
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        if (!($options instanceof Traversable) && !is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'Options must be an array or an instance of Traversable'
            );
        }

        foreach ($options as $option => $value) {
            $method = 'set'
                    . str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($option))));
            if (!method_exists($this, $method)) {
                continue;
            }
            $this->{$method}($value);
        }

        return $this;
    }

    /**
     * Get all pattern options
     *
     * @return array
     */
    public function getOptions()
    {
        return array();
    }
}
