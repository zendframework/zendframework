<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Helper\AbstractHelper;
use ZendTest\View\Helper\TestAsset\ConcreteHelper;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class AbstractTest extends TestCase
{
    /**
     * @var ConcreteHelper
     */
    protected $helper;

    public function setUp()
    {
        $this->helper = new ConcreteHelper();
    }

    public function tearDown()
    {
        AbstractHelper::setDefaultTranslator(null, 'default');
    }

    public function testViewSettersGetters()
    {
        $viewMock = $this->getMock('Zend\View\Renderer\RendererInterface');

        $this->helper->setView($viewMock);
        $this->assertEquals($viewMock, $this->helper->getView());
    }

    public function testTranslatorMethods()
    {
        $translatorMock = $this->getMock('Zend\I18n\Translator\Translator');
        $this->helper->setTranslator($translatorMock, 'foo');

        $this->assertEquals($translatorMock, $this->helper->getTranslator());
        $this->assertEquals('foo', $this->helper->getTranslatorTextDomain());
        $this->assertTrue($this->helper->hasTranslator());
        $this->assertTrue($this->helper->isTranslatorEnabled());

        $this->helper->setTranslatorEnabled(false);
        $this->assertFalse($this->helper->isTranslatorEnabled());
    }

    public function testDefaultTranslatorMethods()
    {
        $this->assertFalse(AbstractHelper::hasDefaultTranslator());
        $this->assertNull(AbstractHelper::getDefaultTranslator());
        $this->assertEquals('default', AbstractHelper::getDefaultTranslatorTextDomain());

        $this->assertFalse($this->helper->hasTranslator());

        $translatorMock = $this->getMock('Zend\I18n\Translator\Translator');
        AbstractHelper::setDefaultTranslator($translatorMock, 'foo');

        $this->assertEquals($translatorMock, AbstractHelper::getDefaultTranslator());
        $this->assertEquals($translatorMock, $this->helper->getTranslator());
        $this->assertEquals('foo', AbstractHelper::getDefaultTranslatorTextDomain());
        $this->assertEquals('foo', $this->helper->getTranslatorTextDomain());
        $this->assertTrue(AbstractHelper::hasDefaultTranslator());
    }
}
