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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Client;

/**
 * @uses       \Zend\Tool\Framework\Client\Exception
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Response
{
    /**
     * @var callback|null
     */
    protected $_callback = null;

    /**
     * @var array
     */
    protected $_content = array();

    /**
     * @var \Zend\Tool\Framework\Exception
     */
    protected $_exception = null;

    /**
     * @var array
     */
    protected $_decorators = array();

    /**
     * @var array
     */
    protected $_defaultDecoratorOptions = array();

    /**
     * setContentCallback()
     *
     * @param callback $callback
     * @return \Zend\Tool\Framework\Client\Response
     */
    public function setContentCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidArgumentException('The callback provided is not callable');
        }
        $this->_callback = $callback;
        return $this;
    }

    /**
     * setContent()
     *
     * @param string $content
     * @return \Zend\Tool\Framework\Client\Response
     */
    public function setContent($content, Array $decoratorOptions = array())
    {
        $this->_applyDecorators($content, $decoratorOptions);

        $this->_content = array();
        $this->appendContent($content);
        return $this;
    }

    /**
     * appendCallback
     *
     * @param string $content
     * @return \Zend\Tool\Framework\Client\Response
     */
    public function appendContent($content, Array $decoratorOptions = array())
    {
        $content = $this->_applyDecorators($content, $decoratorOptions);

        if ($this->_callback !== null) {
            call_user_func($this->_callback, $content);
        }

        $this->_content[] = $content;

        return $this;
    }

    /**
     * setDefaultDecoratorOptions()
     *
     * @param array $decoratorOptions
     * @param bool $mergeIntoExisting
     * @return \Zend\Tool\Framework\Client\Response
     */
    public function setDefaultDecoratorOptions(Array $decoratorOptions, $mergeIntoExisting = false)
    {
        if ($mergeIntoExisting == false) {
            $this->_defaultDecoratorOptions = array();
        }

        $this->_defaultDecoratorOptions = array_merge($this->_defaultDecoratorOptions, $decoratorOptions);
        return $this;
    }

    /**
     * getContent()
     *
     * @return string
     */
    public function getContent()
    {
        return implode('', $this->_content);
    }

    /**
     * isException()
     *
     * @return bool
     */
    public function isException()
    {
        return isset($this->_exception);
    }

    /**
     * setException()
     *
     * @param Exception $exception
     * @return \Zend\Tool\Framework\Client\Response
     */
    public function setException(\Exception $exception)
    {
        $this->_exception = $exception;
        return $this;
    }

    /**
     * getException()
     *
     * @return Exception
     */
    public function getException()
    {
        return $this->_exception;
    }

    /**
     * Add Content Decorator
     *
     * @param \Zend\Tool\Framework\Client\Response\ContentDecorator $contentDecorator
     * @return unknown
     */
    public function addContentDecorator(Response\ContentDecorator $contentDecorator)
    {
        $decoratorName = strtolower($contentDecorator->getName());
        $this->_decorators[$decoratorName] = $contentDecorator;
        return $this;
    }

    /**
     * getContentDecorators()
     *
     * @return array
     */
    public function getContentDecorators()
    {
        return $this->_decorators;
    }

    /**
     * __toString() to cast to a string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) implode('', $this->_content);
    }

    /**
     * _applyDecorators() apply a group of decorators
     *
     * @param string $content
     * @param array $decoratorOptions
     * @return string
     */
    protected function _applyDecorators($content, Array $decoratorOptions)
    {
        $options = array_merge($this->_defaultDecoratorOptions, $decoratorOptions);

        $options = array_change_key_case($options, CASE_LOWER);

        if ($options) {
            foreach ($this->_decorators as $decoratorName => $decorator) {
                if (array_key_exists($decoratorName, $options)) {
                    $content = $decorator->decorate($content, $options[$decoratorName]);
                }
            }
        }

        return $content;

    }

}
