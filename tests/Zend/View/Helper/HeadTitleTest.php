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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;
use Zend\View\Helper\Placeholder\Registry;
use Zend\View\Helper;

/**
 * Test class for Zend_View_Helper_HeadTitle.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $regKey = Registry::REGISTRY_KEY;
        if (\Zend\Registry::isRegistered($regKey)) {
            $registry = \Zend\Registry::getInstance();
            unset($registry[$regKey]);
        }
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
        $placeholder = $this->helper->direct();
        $this->assertTrue($placeholder instanceof Helper\HeadTitle);
    }

    public function testCanSetTitleViaHeadTitle()
    {
        $placeholder = $this->helper->direct('Foo Bar', 'SET');
        $this->assertContains('Foo Bar', $placeholder->toString());
    }

    public function testCanAppendTitleViaHeadTitle()
    {
        $placeholder = $this->helper->direct('Foo');
        $placeholder = $this->helper->direct('Bar');
        $this->assertContains('FooBar', $placeholder->toString());
    }

    public function testCanPrependTitleViaHeadTitle()
    {
        $placeholder = $this->helper->direct('Foo');
        $placeholder = $this->helper->direct('Bar', 'PREPEND');
        $this->assertContains('BarFoo', $placeholder->toString());
    }

    public function testReturnedPlaceholderToStringContainsFullTitleElement()
    {
        $placeholder = $this->helper->direct('Foo');
        $placeholder = $this->helper->direct('Bar', 'APPEND')->setSeparator(' :: ');
        $this->assertEquals('<title>Foo :: Bar</title>', $placeholder->toString());
    }

    public function testToStringEscapesEntries()
    {
        $this->helper->direct('<script type="text/javascript">alert("foo");</script>');
        $string = $this->helper->toString();
        $this->assertNotContains('<script', $string);
        $this->assertNotContains('</script>', $string);
    }

    public function testToStringEscapesSeparator()
    {
        $this->helper->direct('Foo')
                     ->direct('Bar')
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
        $this->helper->direct('foo');
        $string = $this->helper->toString();

        $this->assertContains('    <title>', $string);
    }

    public function testAutoEscapeIsHonored()
    {
        $this->helper->direct('Some Title &copyright;');
        $this->assertEquals('<title>Some Title &amp;copyright;</title>', $this->helper->toString());

        $this->assertTrue($this->helper->direct()->getAutoEscape());
        $this->helper->direct()->setAutoEscape(false);
        $this->assertFalse($this->helper->direct()->getAutoEscape());


        $this->assertEquals('<title>Some Title &copyright;</title>', $this->helper->toString());
    }

    /**
     * @issue ZF-2918
     * @link http://framework.zend.com/issues/browse/ZF-2918
     */
    public function testZF2918()
    {
        $this->helper->direct('Some Title');
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
        $this->helper->direct('Some Title');
        $this->helper->setPrefix('Prefix & ');
        $this->helper->setPostfix(' & Postfix');

        $this->assertEquals('<title>Prefix &amp; Some Title &amp; Postfix</title>', $this->helper->toString());
    }

    public function testCanTranslateTitle()
    {
        $adapter = new \Zend\Translator\Adapter\Ini(__DIR__ . '/../../Translator/Adapter/_files/translation_en.ini', 'en');
        \Zend\Registry::set('Zend_Translate', $adapter);
        $this->helper->enableTranslation();
        $this->helper->direct('Message_1');
        $this->assertEquals('<title>Message 1 (en)</title>', $this->helper->toString());
    }

   /**
    * @see ZF-8036
    */
    public function testHeadTitleZero()
    {
        $this->helper->direct('0');
        $this->assertEquals('<title>0</title>', $this->helper->toString());
    }

    public function testCanPrependTitlesUsingDefaultAttachOrder()
    {
        $this->helper->setDefaultAttachOrder('PREPEND');
        $placeholder = $this->helper->direct('Foo');
        $placeholder = $this->helper->direct('Bar');
        $this->assertContains('BarFoo', $placeholder->toString());
    }
}
