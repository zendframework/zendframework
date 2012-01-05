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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator;

/**
 * @uses       \Zend\Validator\AbstractValidator
 * @uses       \Zend\Validator\Exception
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Callback extends AbstractValidator
{
    /**
     * Invalid callback
     */
    const INVALID_CALLBACK = 'callbackInvalid';

    /**
     * Invalid value
     */
    const INVALID_VALUE = 'callbackValue';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_VALUE    => "'%value%' is not valid",
        self::INVALID_CALLBACK => "An exception has been raised within the callback",
    );

    /**
     * Default options to set for the validator
     *
     * @var mixed
     */
    protected $options = array(
        'callback'         => null,     // Callback in a call_user_func format, string || array
        'callbackOptions'  => array(),  // Options for the callback
    );

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_callable($options)) {
            $options = array('callback' => $options);
        }

        parent::__construct($options);
    }

    /**
     * Returns the set callback
     *
     * @return mixed
     */
    public function getCallback()
    {
        return $this->options['callback'];
    }

    /**
     * Sets the callback
     *
     * @param  string|array $callback
     * @return \Zend\Validator\Callback Provides a fluent interface
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidArgumentException('Invalid callback given');
        }

        $this->options['callback'] = $callback;
        return $this;
    }

    /**
     * Returns the set options for the callback
     *
     * @return mixed
     */
    public function getCallbackOptions()
    {
        return $this->options['callbackOptions'];
    }

    /**
     * Sets options for the callback
     *
     * @param  mixed $max
     * @return \Zend\Validator\Callback Provides a fluent interface
     */
    public function setCallbackOptions($options)
    {
        $this->options['callbackOptions'] = (array) $options;
        return $this;
    }

    /**
     * Returns true if and only if the set callback returns
     * for the provided $value
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $options  = $this->getCallbackOptions();
        $callback = $this->getCallback();
        if (empty($callback)) {
            throw new Exception\InvalidArgumentException('No callback given');
        }

        $args     = func_get_args();
        $options  = array_merge($args, $options);

        try {
            if (!call_user_func_array($callback, $options)) {
                $this->error(self::INVALID_VALUE);
                return false;
            }
        } catch (\Exception $e) {
            $this->error(self::INVALID_CALLBACK);
            return false;
        }

        return true;
    }
}
