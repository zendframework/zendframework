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

/**
 * @namespace
 */
namespace Zend\View\Helper\Placeholder;

use Zend\View\Exception;

/**
 * Registry for placeholder containers
 *
 * @uses       ReflectionClass
 * @uses       \Zend\Loader
 * @uses       \Zend\Registry
 * @uses       \Zend\View\Helper\Placeholder\Container
 * @uses       \Zend\View\Helper\Placeholder\Container\AbstractContainer
 * @uses       \Zend\View\Helper\Placeholder\Registry\Exception
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Registry
{
    /**
     * Zend_Registry key under which placeholder registry exists
     * @const string
     */
    const REGISTRY_KEY = 'Zend\View\Helper\Placeholder\Registry';

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
     * @return mixed
     */
    public static function getRegistry()
    {
        if (\Zend\Registry::isRegistered(self::REGISTRY_KEY)) {
            $registry = \Zend\Registry::get(self::REGISTRY_KEY);
        } else {
            $registry = new self();
            \Zend\Registry::set(self::REGISTRY_KEY, $registry);
        }

        return $registry;
    }

    /**
     * createContainer
     *
     * @param  string $key
     * @param  array $value
     * @return \Zend\View\Helper\Placeholder\Container\AbstractContainer
     */
    public function createContainer($key, array $value = array())
    {
        $key = (string) $key;

        $this->_items[$key] = new $this->_containerClass(array());
        return $this->_items[$key];
    }

    /**
     * Retrieve a placeholder container
     *
     * @param  string $key
     * @return \Zend\View\Helper\Placeholder\Container\AbstractContainer
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
     * @param  Zend\View\Placeholder\Container\AbstractContainer $container
     * @return Zend\View\Placeholder\Registry
     */
    public function setContainer($key, \Zend\View\Helper\Placeholder\Container\AbstractContainer $container)
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
     * @return \Zend\View\Helper\Placeholder\Registry
     * @throws Exception\InvalidArgumentException
     */
    public function setContainerClass($name)
    {
        if (!class_exists($name)) {
            \Zend\Loader::loadClass($name);
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
