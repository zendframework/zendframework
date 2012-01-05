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
 * Front Controller resource
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FrontController extends AbstractResource
{
    /**
     * @var \Zend\Controller\Front
     */
    protected $_front;

    /**
     * Initialize Front Controller
     *
     * @return \Zend\Controller\Front
     */
    public function init()
    {
        $front = $this->getFrontController();

        foreach ($this->getOptions() as $key => $value) {
            switch (strtolower($key)) {
                case 'controllerdirectory':
                    if (is_string($value)) {
                        $front->setControllerDirectory($value);
                    } elseif (is_array($value)) {
                        foreach ($value as $module => $directory) {
                            $front->addControllerDirectory($directory, $module);
                        }
                    }
                    break;

                case 'modulecontrollerdirectoryname':
                    $front->setModuleControllerDirectoryName($value);
                    break;

                case 'moduledirectory':
                    $front->addModuleDirectory($value);
                    break;

                case 'defaultcontrollername':
                    $front->setDefaultControllerName($value);
                    break;

                case 'defaultaction':
                    $front->setDefaultAction($value);
                    break;

                case 'defaultmodule':
                    $front->setDefaultModule($value);
                    break;

                case 'baseurl':
                    if (!empty($value)) {
                        $front->setBaseUrl($value);
                    }
                    break;

                case 'params':
                    $front->setParams($value);
                    break;

                case 'plugins':
                    foreach ((array) $value as $pluginClass) {
                    	$stackIndex = null;
                    	if(is_array($pluginClass)) {
                    	    $pluginClass = array_change_key_case($pluginClass, CASE_LOWER);
                            if(isset($pluginClass['class']))
                            {
                                if(isset($pluginClass['stackindex'])) {
                                    $stackIndex = $pluginClass['stackindex'];
                                }

                                $pluginClass = $pluginClass['class'];
                            }
                        }

                        $plugin = new $pluginClass();
                        $front->registerPlugin($plugin, $stackIndex);
                    }
                    break;

                case 'returnresponse':
                    $front->returnResponse((bool) $value);
                    break;

                case 'throwexceptions':
                    $front->throwExceptions((bool) $value);
                    break;

                case 'action_helper':
                case 'actionhelper':
                    if (is_string($value)) {
                        $front->setHelperBroker($value);
                    } elseif (is_array($value)) {
                        if (isset($value['class'])) {
                            if (!class_exists($value['class'])) {
                                throw new Exception(sprintf(
                                    'Could not resolve action helper class "%s"',
                                    $value['class']
                                ));
                            }
                            $class = $value['class'];
                            unset($value['class']);
                            $broker = new $class($value);
                            $front->setHelperBroker($broker);
                        }
                    }
                    break;

                default:
                    $front->setParam($key, $value);
                    break;
            }
        }

        if (null !== ($bootstrap = $this->getBootstrap())) {
            $this->getBootstrap()->frontController = $front;
        }

        return $front;
    }

    /**
     * Retrieve front controller instance
     *
     * @return \Zend\Controller\Front
     */
    public function getFrontController()
    {
        if (null === $this->_front) {
            $this->_front = \Zend\Controller\Front::getInstance();
        }
        return $this->_front;
    }
}
