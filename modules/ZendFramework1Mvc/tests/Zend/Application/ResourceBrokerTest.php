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

namespace ZendTest\Application;

use Zend\Application\ResourceBroker,
    Zend\Application\ResourceLoader,
    Zend\Application\Application,
    Zend\Application\Bootstrap;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class ResourceBrokerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->broker = new ResourceBroker();
        $this->application = new Application('testing', array());
        $this->bootstrap   = new Bootstrap($this->application);
    }

    public function testBootstrapIsUndefinedByDefault()
    {
        $this->assertNull($this->broker->getBootstrap());
    }

    public function testCanSetBootstrap()
    {
        $this->broker->setBootstrap($this->bootstrap);
        $this->assertSame($this->bootstrap, $this->broker->getBootstrap());
    }

    public function testNoBootstrapInjectedInResourceIfNotInjectedInBroker()
    {
        $this->broker->registerSpec('view');
        $view = $this->broker->load('view');
        $this->assertInstanceOf('Zend\Application\Resource\View', $view);
        $this->assertNull($view->getBootstrap());
    }

    public function testBootstrapInjectedInResourceIfInjectedInBroker()
    {
        $this->broker->setBootstrap($this->bootstrap);
        $this->broker->registerSpec('view');
        $view = $this->broker->load('view');
        $this->assertInstanceOf('Zend\Application\Resource\View', $view);
        $this->assertSame($this->bootstrap, $view->getBootstrap());
    }

    public function testExceptionIsRaisedIfLoadedPluginIsNotAnApplicationResource()
    {
        $this->broker->getClassLoader()->registerPlugin('view', 'Zend\View\PhpRenderer');
        $this->setExpectedException('InvalidArgumentException', 'must implement');
        $view = $this->broker->load('view');
    }
}
