<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View;

use Zend\ServiceManager\ServiceManager;
use Zend\View\HelperPluginManager;
use Zend\View\Renderer\PhpRenderer;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 */
class HelperPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->helpers = new HelperPluginManager();
    }

    public function testViewIsNullByDefault()
    {
        $this->assertNull($this->helpers->getRenderer());
    }

    public function testAllowsInjectingRenderer()
    {
        $renderer = new PhpRenderer();
        $this->helpers->setRenderer($renderer);
        $this->assertSame($renderer, $this->helpers->getRenderer());
    }

    public function testInjectsRendererToHelperWhenRendererIsPresent()
    {
        $renderer = new PhpRenderer();
        $this->helpers->setRenderer($renderer);
        $helper = $this->helpers->get('doctype');
        $this->assertSame($renderer, $helper->getView());
    }

    public function testNoRendererInjectedInHelperWhenRendererIsNotPresent()
    {
        $helper = $this->helpers->get('doctype');
        $this->assertNull($helper->getView());
    }

    public function testRegisteringInvalidHelperRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception\InvalidHelperException');
        $this->helpers->setService('test', $this);
    }

    public function testLoadingInvalidHelperRaisesException()
    {
        $this->helpers->setInvokableClass('test', get_class($this));
        $this->setExpectedException('Zend\View\Exception\InvalidHelperException');
        $this->helpers->get('test');
    }

    public function testDefinesFactoryForIdentityPlugin()
    {
        $this->assertTrue($this->helpers->has('identity'));
    }

    public function testIdentityFactoryCanInjectAuthenticationServiceIfInParentServiceManager()
    {
        $services = new ServiceManager();
        $services->setInvokableClass('Zend\Authentication\AuthenticationService', 'Zend\Authentication\AuthenticationService');
        $this->helpers->setServiceLocator($services);
        $identity = $this->helpers->get('identity');
        $expected = $services->get('Zend\Authentication\AuthenticationService');
        $this->assertSame($expected, $identity->getAuthenticationService());
    }
}
