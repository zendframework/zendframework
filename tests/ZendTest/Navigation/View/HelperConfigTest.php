<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Navigation\View;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Navigation\View\HelperConfig;

/**
 * Tests the class Zend_Navigation_Page_Mvc
 *
 * @group      Zend_Navigation
 */
class HelperConfigTest extends TestCase
{
    protected $pluginManager = null;
    protected $serviceManager = null;
    protected $helperConfig = null;

    public function setUp()
    {
        $this->serviceManager = new ServiceManager();

        $this->pluginManager = new \Zend\View\HelperPluginManager();
        $this->pluginManager->setServiceLocator($this->serviceManager);

        $this->helperConfig = new HelperConfig();
    }

    public function testConfigureServiceManagerWithConfig()
    {
        $replacedMenuClass = 'Zend\View\Helper\Navigation\Links';
        $this->serviceManager->setService('config', array('navigation_helpers' => array(
            'invokables' => array(
                'menu' => $replacedMenuClass
             )
        )));
        $this->helperConfig->configureServiceManager($this->pluginManager);

        $menu = $this->pluginManager->get('navigation')->findHelper('menu');
        $this->assertInstanceOf($replacedMenuClass, $menu);
    }
}
