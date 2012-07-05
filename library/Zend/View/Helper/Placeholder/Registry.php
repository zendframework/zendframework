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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Helper\Placeholder;

use Zend\View\Exception;

/**
 * Registry for placeholder containers
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Registry
{
    /**
     * @var Registry Singleton instance 
     */
    protected static $instance;

    /**
     * Default container class
     * @var string
     */
    protected $_containerClass = 'Zend\View\Helper\Placeholder\Container';

    /**
     * Placeholder containers
     * @var array
     */
    protected $_items = array();

    /**
     * Retrieve or create registry instance
     *
     * @return Registry
     */
    public static function getRegistry()
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Unset the singleton
     *
     * Primarily useful for testing purposes; sets {@link $instance} to null.
     * 
     * @return void
     */
    public static function unsetRegistry()
    {
        static::$instance = null;
    }

    /**
     * createContainer
     *
     * @param  string $key
     * @param  array $value
     * @return Container\AbstractContainer
     */
    public function createContainer($key, array $value = array())
    {
        $key = (string) $key;

        $this->_items[$key] = new $this->_containerClass($value);
        return $this->_items[$key];
    }

    /**
     * Retrieve a placeholder container
     *
     * @param  string $key
     * @return Container\AbstractContainer
     */
    public function getContainer($key)
    {
        $key = (string) $key;
        if (isset($this->_items[$key])) {
            return $this->_items[$key];
        }

        $container = $this->createContainer($key);

        return $container;
    }

    /**
     * Does a particular container exist?
     *
     * @param  string $key
     * @return bool
     */
    public function containerExists($key)
    {
        $key = (string) $key;
        $return =  array_key_exists($key, $this->_items);
        return $return;
    }

    /**
     * Set the container for an item in the registry
     *
     * @param  string $key
     * @param  Container\AbstractContainer $container
     * @return Registry
     */
    public function setContainer($key, Container\AbstractContainer $container)
    {
        $key = (string) $key;
        $this->_items[$key] = $container;
        return $this;
    }

    /**
     * Delete a container
     *
     * @param  string $key
     * @return bool
     */
    public function deleteContainer($key)
    {
        $key = (string) $key;
        if (isset($this->_items[$key])) {
            unset($this->_items[$key]);
            return true;
        }

        return false;
    }

    /**
     * Set the container class to use
     *
     * @param  string $name
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     * @return Registry
     */
    public function setContainerClass($name)
    {
        if (!class_exists($name)) {
            throw new Exception\DomainException(
                sprintf('%s expects a valid registry class name; received "%s", which did not resolve',
                        __METHOD__,
                        $name
                ));
        }

        if (!in_array('Zend\View\Helper\Placeholder\Container\AbstractContainer', class_parents($name))) {
            throw new Exception\InvalidArgumentException('Invalid Container class specified');
        }

        $this->_containerClass = $name;
        return $this;
    }

    /**
     * Retrieve the container class
     *
     * @return string
     */
    public function getContainerClass()
    {
        return $this->_containerClass;
    }
}
