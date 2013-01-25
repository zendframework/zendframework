<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use Zend\I18n\Translator\Translator;
use Zend\View\Helper\Placeholder\Registry;
use Zend\View\Helper;

/**
 * Test class for Zend_View_Helper_HeadTitle.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class HeadTitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_HeadTitle
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::unsetRegistry();
        $this->basePath = __DIR__ . '/_files/modules';
        $this->helper = new Helper\HeadTitle();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testNamespaceRegisteredInPlaceholderRegistryAfterInstantiation()
    {
        $registry = Registry::getRegistry();
        if ($registry->containerExists('Zend_View_Helper_HeadTitle')) {
            $registry->deleteContainer('Zend_View_Helper_HeadTitle');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_HeadTitle'));
        $helper = new Helper\HeadTitle();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_HeadTitle'));
    }

    public function testHeadTitleReturnsObjectInstance()
    {
        $placeholder = $this->helper->__invoke();
        $this->assertTrue($placeholder instanceof Helper\HeadTitle);
    }

    public function testCanSetTitleViaHeadTitle()
    {
        $placeholder = $this->helper->__invoke('Foo Bar', 'SET');
        $this->assertContains('Foo Bar', $placeholder->toString());
    }

    public function testCanAppendTitleViaHeadTitle()
    {
        $placeholder = $this->helper->__invoke('Foo');
        $placeholder = $this->helper->__invoke('Bar');
        $this->assertContains('FooBar', $placeholder->toString());
    }

    public function testCanPrependTitleViaHeadTitle()
    {
        $placeholder = $this->helper->__invoke('Foo');
        $placeholder = $this->helper->__invoke('Bar', 'PREPEND');
        $this->assertContains('BarFoo', $placeholder->toString());
    }

    public function testReturnedPlaceholderToStringContainsFullTitleElement()
    {
        $placeholder = $this->helper->__invoke('Foo');
        $placeholder = $this->helper->__invoke('Bar', 'APPEND')->setSeparator(' :: ');
        $this->assertEquals('<title>Foo :: Bar</title>', $placeholder->toString());
    }

    public function testToStringEscapesEntries()
    {
        $this->helper->__invoke('<script type="text/javascript">alert("foo");</script>');
        $string = $this->helper->toString();
        $this->assertNotContains('<script', $string);
        $this->assertNotContains('</script>', $string);
    }

    public function testToStringEscapesSeparator()
    {
        $this->helper->__invoke('Foo')
                     ->__invoke('Bar')
                     ->setSeparator(' <br /> ');
        $string = $this->helper->toString();
        $this->assertNotContains('<br />', $string);
        $this->assertContains('Foo', $string);
        $this->assertContains('Bar', $string);
        $this->assertContains('br /', $string);
    }

    public function testIndentationIsHonored()
    {
        $this->helper->setIndent(4);
        $this->helper->__invoke('foo');
        $string = $this->helper->toString();

        $this->assertContains('    <title>', $string);
    }

    public function testAutoEscapeIsHonored()
    {
        $this->helper->__invoke('Some Title &copyright;');
        $this->assertEquals('<title>Some Title &amp;copyright;</title>', $this->helper->toString());

        $this->assertTrue($this->helper->__invoke()->getAutoEscape());
        $this->helper->__invoke()->setAutoEscape(false);
        $this->assertFalse($this->helper->__invoke()->getAutoEscape());


        $this->assertEquals('<title>Some Title &copyright;</title>', $this->helper->toString());
    }

    /**
     * @issue ZF-2918
     * @link http://framework.zend.com/issues/browse/ZF-2918
     */
    public function testZF2918()
    {
        $this->helper->__invoke('Some Title');
        $this->helper->setPrefix('Prefix: ');
        $this->helper->setPostfix(' :Postfix');

        $this->assertEquals('<title>Prefix: Some Title :Postfix</title>', $this->helper->toString());
    }

    /**
     * @issue ZF-3577
     * @link http://framework.zend.com/issues/browse/ZF-3577
     */
    public function testZF3577()
    {
        $this->helper->setAutoEscape(true);
        $this->helper->__invoke('Some Title');
        $this->helper->setPrefix('Prefix & ');
        $this->helper->setPostfix(' & Postfix');

        $this->assertEquals('<title>Prefix &amp; Some Title &amp; Postfix</title>', $this->helper->toString());
    }

    public function testCanTranslateTitle()
    {
        $loader = new TestAsset\ArrayTranslator();
        $loader->translations = array(
            'Message_1' => 'Message 1 (en)',
        );
        $translator = new Translator();
        $translator->getPluginManager()->setService('default', $loader);
        $translator->addTranslationFile('default', null);

        $this->helper->setTranslatorEnabled(true);
        $this->helper->setTranslator($translator);
        $this->helper->__invoke('Message_1');
        $this->assertEquals('<title>Message 1 (en)</title>', $this->helper->toString());
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

    /**
     * @group ZF-8036
     */
    public function testHeadTitleZero()
    {
        $this->helper->__invoke('0');
        $this->assertEquals('<title>0</title>', $this->helper->toString());
    }

    public function testCanPrependTitlesUsingDefaultAttachOrder()
    {
        $this->helper->setDefaultAttachOrder('PREPEND');
        $placeholder = $this->helper->__invoke('Foo');
        $placeholder = $this->helper->__invoke('Bar');
        $this->assertContains('BarFoo', $placeholder->toString());
    }


    /**
     *  @group ZF-10284
     */
    public function testReturnTypeDefaultAttachOrder()
    {
        $this->assertTrue($this->helper->setDefaultAttachOrder('PREPEND') instanceof Helper\HeadTitle);
        $this->assertEquals('PREPEND', $this->helper->getDefaultAttachOrder());
    }
}
