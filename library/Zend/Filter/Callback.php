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

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Callback extends AbstractFilter
{
    /**
     * @var array
     */
    protected $options = array(
        'callback'        => null,
        'callback_params' => array()
    );

    /**
     * @param array|Traversable $options
     */
    public function __construct($callbackOrOptions, $callbackParams = array())
    {
        if (is_callable($callbackOrOptions)) {
            $this->setCallback($callbackOrOptions);
            $this->setCallbackParams($callbackParams);
        } else {
            $this->setOptions($callbackOrOptions);
        }
    }

    /**
     * Sets a new callback for this filter
     *
     * @param  callable $callback
     * @return Callback
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter for callback: must be callable'
            );
        }

        $this->options['callback'] = $callback;
        return $this;
    }

    /**
     * Returns the set callback
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->options['callback'];
    }

    /**
     * Sets parameters for the callback
     *
     * @param  mixed $params
     * @return Callback
     */
    public function setCallbackParams($params)
    {
        $this->options['callback_params'] = (array) $params;
        return $this;
    }

    /**
     * Get parameters for the callback
     *
     * @return mixed
     */
    public function getCallbackParams()
    {
        return $this->options['callback_params'];
    }

    /**
     * Calls the filter per callback
     *
     * @param  mixed $value Options for the set callback
     * @return mixed Result from the filter which was callbacked
     */
    public function filter($value)
    {
        $params = (array) $this->options['callback_params'];
        array_unshift($params, $value);

        return call_user_func_array($this->options['callback'], $params);
    }
}
