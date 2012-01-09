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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Application\Resource;

use Zend\Loader\Autoloader,
    Zend\Application\Resource\Dojo as DojoResource,
    Zend\Application,
    Zend\Controller\Front as FrontController,
    Zend\Dojo\View\Helper\Dojo\Container as DojoContainer;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class DojoTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application\Application('testing');

        $this->bootstrap = new Application\Bootstrap($this->application);
        $this->bootstrap->getBroker()->registerSpec('view');

        FrontController::getInstance()->resetInstance();
    }

    public function tearDown()
    {
    }

    public function testInitializationInitializesDojoContainer()
    {
        $resource = new DojoResource(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue($resource->getDojo() instanceof DojoContainer);
    }

    public function testInitializationReturnsDojoContainer()
    {
        $resource = new DojoResource(array());
        $resource->setBootstrap($this->bootstrap);
        $test = $resource->init();
        $this->assertTrue($test instanceof DojoContainer);
    }

    public function testOptionsPassedToResourceAreUsedToSetDojosContainerState()
    {
        $options = array(
            'requireModules'     => array('DojoTest'),
            'localPath'          => '/ofc/ZF/Rules/',
        );

        $resource = new DojoResource($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $resource->getBootstrap()->bootstrap('view');
        $dojo = $resource->getBootstrap()->view->dojo();

        $test = array(
            'requireModules' => $dojo->getModules(),
            'localPath'      => $dojo->getLocalPath()
        );
        $this->assertEquals($options, $test);
    }
}
