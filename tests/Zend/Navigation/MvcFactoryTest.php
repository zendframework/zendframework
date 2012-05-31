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
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Navigation;

use Zend\Config;
use Zend\Navigation;
use Zend\Navigation\MvcNavigationFactory;

/**
 * Tests the class Zend\Navigation\MvcNavigationFactory
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Navigation
 */
class MvcFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $configuration = array(
            'modules' => array(),
            'module_listener_options' => array(),
            'service_manager' => array(),
        );

        $serviceConfig  = new \Zend\Mvc\Service\ServiceManagerConfiguration($configuration['service_manager']);
        $serviceManager = new \Zend\ServiceManager\ServiceManager($serviceConfig);
        $serviceManager->setService('ApplicationConfiguration', $configuration);
        $serviceManager->get('ModuleManager')->loadModules();
        $serviceManager->get('Application')->bootstrap();

        $this->serviceManager = $serviceManager;
    }

    /**
     * Tear down the environment after running a test
     */
    protected function tearDown()
    {

    }

    /**
     * @covers \Zend\Navigation\MvcNavigationFactory
     */
    public function testConstructFromArray()
    {
        $argument = array(
            array(
                'label' => 'Page 1',
                'uri'   => 'page1.html'
            ),
            array(
                'label' => 'Page 2',
                'uri'   => 'page2.html'
            ),
            array(
                'label' => 'Page 3',
                'uri'   => 'page3.html'
            )
        );

        $factory = new MvcNavigationFactory($argument);
        $this->serviceManager->setFactory('MvcNavigation', $factory);

        $container = $this->serviceManager->get('MvcNavigation');
        $this->assertEquals(3, $container->count());
    }

    /**
     * @covers \Zend\Navigation\MvcNavigationFactory
     */
    public function testConstructFromFileString()
    {
        $argument = __DIR__ . '/_files/navigation.xml';
        $factory  = new MvcNavigationFactory($argument);
        $this->serviceManager->setFactory('MvcNavigation', $factory);

        $container = $this->serviceManager->get('MvcNavigation');
        $this->assertEquals(3, $container->count());
    }

    /**
     * @covers \Zend\Navigation\MvcNavigationFactory
     */
    public function testConstructFromConfig()
    {
        $argument = new Config\Config(array(
            array(
                'label' => 'Page 1',
                'uri'   => 'page1.html'
            ),
            array(
                'label' => 'Page 2',
                'uri'   => 'page2.html'
            ),
            array(
                'label' => 'Page 3',
                'uri'   => 'page3.html'
            )
        ));

        $factory = new MvcNavigationFactory($argument);
        $this->serviceManager->setFactory('MvcNavigation', $factory);

        $container = $this->serviceManager->get('MvcNavigation');
        $this->assertEquals(3, $container->count());
    }
}
