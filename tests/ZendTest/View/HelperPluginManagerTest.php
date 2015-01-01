<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View;

use Zend\I18n\Translator\Translator;
use Zend\Mvc\I18n\Translator as MvcTranslator;
use Zend\ServiceManager\ServiceManager;
use Zend\View\HelperPluginManager;
use Zend\View\Renderer\PhpRenderer;

/**
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

    public function testIfHelperIsTranslatorAwareAndMvcTranslatorIsAvailableItWillInjectTheMvcTranslator()
    {
        $translator = new MvcTranslator($this->getMock('Zend\I18n\Translator\TranslatorInterface'));
        $services   = new ServiceManager();
        $services->setService('MvcTranslator', $translator);
        $this->helpers->setServiceLocator($services);

        $helper = $this->helpers->get('HeadTitle');
        $this->assertSame($translator, $helper->getTranslator());
    }

    public function testIfHelperIsTranslatorAwareAndMvcTranslatorIsUnavailableAndTranslatorIsAvailableItWillInjectTheTranslator()
    {
        $translator = new Translator();
        $services   = new ServiceManager();
        $services->setService('Translator', $translator);
        $this->helpers->setServiceLocator($services);

        $helper = $this->helpers->get('HeadTitle');
        $this->assertSame($translator, $helper->getTranslator());
    }

    public function testIfHelperIsTranslatorAwareAndBothMvcTranslatorAndTranslatorAreUnavailableAndTranslatorInterfaceIsAvailableItWillInjectTheTranslator()
    {
        $translator = new Translator();
        $services   = new ServiceManager();
        $services->setService('Zend\I18n\Translator\TranslatorInterface', $translator);
        $this->helpers->setServiceLocator($services);

        $helper = $this->helpers->get('HeadTitle');
        $this->assertSame($translator, $helper->getTranslator());
    }
}
