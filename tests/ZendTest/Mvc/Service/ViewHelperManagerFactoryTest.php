<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Service\ViewHelperManagerFactory;
use Zend\ServiceManager\ServiceManager;

class ViewHelperManagerFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->services = new ServiceManager();
        $this->factory  = new ViewHelperManagerFactory();
    }

    /**
     * @return array
     */
    public function emptyConfiguration()
    {
        return array(
            'no-config'                => array(array()),
            'view-manager-config-only' => array(array('view_manager' => array())),
            'empty-doctype-config'     => array(array('view_manager' => array('doctype' => null))),
        );
    }

    /**
     * @dataProvider emptyConfiguration
     * @param  array $config
     * @return void
     */
    public function testDoctypeFactoryDoesNotRaiseErrorOnMissingConfiguration($config)
    {
        $this->services->setService('Config', $config);
        $manager = $this->factory->createService($this->services);
        $this->assertInstanceof('Zend\View\HelperPluginManager', $manager);
        $doctype = $manager->get('doctype');
        $this->assertInstanceof('Zend\View\Helper\Doctype', $doctype);
    }

    public function testConsoleRequestsResultInSilentFailure()
    {
        $this->services->setService('Config', array());
        $this->services->setService('Request', new ConsoleRequest());

        $manager = $this->factory->createService($this->services);

        $doctype = $manager->get('doctype');
        $this->assertInstanceof('Zend\View\Helper\Doctype', $doctype);

        $basePath = $manager->get('basepath');
        $this->assertInstanceof('Zend\View\Helper\BasePath', $basePath);
    }

    /**
     * @group 6247
     */
    public function testConsoleRequestWithBasePathConsole()
    {
        $this->services->setService('Config',
            array(
                'view_manager' => array(
                    'base_path_console' => 'http://test.com'
                )
            )
        );
        $this->services->setService('Request', new ConsoleRequest());

        $manager = $this->factory->createService($this->services);

        $basePath = $manager->get('basepath');
        $this->assertEquals('http://test.com', $basePath());
    }
}
