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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter;

use Traversable,
    Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Callback extends AbstractFilter
{
    /**
     * Callback in a call_user_func format
     *
     * @var string|array
     */
    protected $_callback = null;

    /**
     * Default options to set for the filter
     *
     * @var mixed
     */
    protected $_options = null;

    /**
     * Constructor
     *
     * @param string|array $callback Callback in a call_user_func format
     * @param mixed        $options  (Optional) Default options for this filter
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options) || !array_key_exists('callback', $options)) {
            $options          = func_get_args();
            $temp['callback'] = array_shift($options);
            if (!empty($options)) {
                $temp['options'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('callback', $options)) {
            throw new Exception\InvalidArgumentException('Missing callback to use');
        }

        $this->setCallback($options['callback']);
        if (array_key_exists('options', $options)) {
            $this->setOptions($options['options']);
        }
    }

    /**
     * Returns the set callback
     *
     * @return string|array Set callback
     */
    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * Sets a new callback for this filter
     *
     * @param \callable $callback
     * @return Callback
     */
    public function setCallback($callback, $options = null)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidArgumentException('Callback can not be accessed');
        }

        $this->_callback = $callback;
        $this->setOptions($options);
        return $this;
    }

    /**
     * Returns the set default options
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets new default options to the callback filter
     *
     * @param mixed $options Default options to set
     * @return Callback
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Calls the filter per callback
     *
     * @param  mixed $value Options for the set callback
     * @return mixed Result from the filter which was callbacked
     */
    public function filter($value)
    {
        $options = array();

        if ($this->_options !== null) {
            if (!is_array($this->_options)) {
                $options = array($this->_options);
            } else {
                $options = $this->_options;
            }
        }

        array_unshift($options, $value);

        return call_user_func_array($this->_callback, $options);
    }
}
