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
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

/**
 * Resource for setting navigation structure
 *
 * @uses       \Zend\Application\Resource\AbstractResource
 * @uses       \Zend\Navigation\Navigation
 * @uses       \Zend\Registry
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Dolf Schimmel
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Navigation
    extends AbstractResource
{
    const DEFAULT_REGISTRY_KEY = 'Zend_Navigation';

    /**
     * @var \Zend\Navigation\Navigation
     */
    protected $_container;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return \Zend\Navigation\Navigation
     */
    public function init()
    {
        if (!$this->_container) {
            $options = $this->getOptions();
            $pages = isset($options['pages']) ? $options['pages'] : array();
            $this->_container = new \Zend\Navigation\Navigation($pages);
        }

        $this->store();
        return $this->_container;
    }

    /**
     * Stores navigation container in registry or Navigation view helper
     *
     * @return void
     */
    public function store()
    {
        $options = $this->getOptions();
        if (isset($options['storage']['registry']) &&
            $options['storage']['registry'] == true) {
            $this->_storeRegistry();
        } else {
            $this->_storeHelper();
        }
    }

    /**
     * Stores navigation container in the registry
     *
     * @return void
     */
    protected function _storeRegistry()
    {
        $options = $this->getOptions();
        if(isset($options['storage']['registry']['key']) &&
           !is_numeric($options['storage']['registry']['key'])) // see ZF-7461
        {
           $key = $options['storage']['registry']['key'];
        } else {
            $key = self::DEFAULT_REGISTRY_KEY;
        }

        \Zend\Registry::set($key,$this->getContainer());
    }

    /**
     * Stores navigation container in the Navigation helper
     *
     * @return void
     */
    protected function _storeHelper()
    {
        $this->getBootstrap()->bootstrap('view');
        $view = $this->getBootstrap()->view;
        $view->plugin('navigation')->setContainer($this->getContainer());
    }

    /**
     * Returns navigation container
     *
     * @return \Zend\Navigation\Navigation
     */
    public function getContainer()
    {
        return $this->_container;
    }
}
