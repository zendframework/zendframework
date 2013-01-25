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

use Zend\View\Helper\Placeholder\Registry as PlaceholderRegistry;
use Zend\View\Renderer\PhpRenderer as View;
use Zend\View\Helper;
use Zend\View\Exception\ExceptionInterface as ViewException;

/**
 * Test class for Zend_View_Helper_HeadMeta.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class HeadMetaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_HeadMeta
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
        $this->error = false;
        PlaceholderRegistry::unsetRegistry();
        Helper\Doctype::unsetDoctypeRegistry();
        $this->basePath = __DIR__ . '/_files/modules';
        $this->view     = new View();
        $this->view->plugin('doctype')->__invoke('XHTML1_STRICT');
        $this->helper   = new Helper\HeadMeta();
        $this->helper->setView($this->view);
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

    public function handleErrors($errno, $errstr)
    {
        $this->error = $errstr;
    }

    public function testNamespaceRegisteredInPlaceholderRegistryAfterInstantiation()
    {
        $registry = PlaceholderRegistry::getRegistry();
        if ($registry->containerExists('Zend_View_Helper_HeadMeta')) {
            $registry->deleteContainer('Zend_View_Helper_HeadMeta');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_HeadMeta'));
        $helper = new Helper\HeadMeta();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_HeadMeta'));
    }

    public function testHeadMetaReturnsObjectInstance()
    {
        $placeholder = $this->helper->__invoke();
        $this->assertTrue($placeholder instanceof Helper\HeadMeta);
    }

    public function testAppendPrependAndSetThrowExceptionsWhenNonMetaValueProvided()
    {
        try {
            $this->helper->append('foo');
            $this->fail('Non-meta value should not append');
        } catch (ViewException $e) {
        }
        try {
            $this->helper->offsetSet(3, 'foo');
            $this->fail('Non-meta value should not offsetSet');
        } catch (ViewException $e) {
        }
        try {
            $this->helper->prepend('foo');
            $this->fail('Non-meta value should not prepend');
        } catch (ViewException $e) {
        }
        try {
            $this->helper->set('foo');
            $this->fail('Non-meta value should not set');
        } catch (ViewException $e) {
        }
    }

    protected function _inflectAction($type)
    {
        $type = str_replace('-', ' ', $type);
        $type = ucwords($type);
        $type = str_replace(' ', '', $type);
        return $type;
    }

    protected function _testOverloadAppend($type)
    {
        $action = 'append' . $this->_inflectAction($type);
        $string = 'foo';
        for ($i = 0; $i < 3; ++$i) {
            $string .= ' foo';
            $this->helper->$action('keywords', $string);
            $values = $this->helper->getArrayCopy();
            $this->assertEquals($i + 1, count($values));

            $item   = $values[$i];
            $this->assertObjectHasAttribute('type', $item);
            $this->assertObjectHasAttribute('modifiers', $item);
            $this->assertObjectHasAttribute('content', $item);
            $this->assertObjectHasAttribute($item->type, $item);
            $this->assertEquals('keywords', $item->{$item->type});
            $this->assertEquals($string, $item->content);
        }
    }

    protected function _testOverloadPrepend($type)
    {
        $action = 'prepend' . $this->_inflectAction($type);
        $string = 'foo';
        for ($i = 0; $i < 3; ++$i) {
            $string .= ' foo';
            $this->helper->$action('keywords', $string);
            $values = $this->helper->getArrayCopy();
            $this->assertEquals($i + 1, count($values));
            $item = array_shift($values);

            $this->assertObjectHasAttribute('type', $item);
            $this->assertObjectHasAttribute('modifiers', $item);
            $this->assertObjectHasAttribute('content', $item);
            $this->assertObjectHasAttribute($item->type, $item);
            $this->assertEquals('keywords', $item->{$item->type});
            $this->assertEquals($string, $item->content);
        }
    }

    protected function _testOverloadSet($type)
    {
        $setAction = 'set' . $this->_inflectAction($type);
        $appendAction = 'append' . $this->_inflectAction($type);
        $string = 'foo';
        for ($i = 0; $i < 3; ++$i) {
            $this->helper->$appendAction('keywords', $string);
            $string .= ' foo';
        }
        $this->helper->$setAction('keywords', $string);
        $values = $this->helper->getArrayCopy();
        $this->assertEquals(1, count($values));
        $item = array_shift($values);

        $this->assertObjectHasAttribute('type', $item);
        $this->assertObjectHasAttribute('modifiers', $item);
        $this->assertObjectHasAttribute('content', $item);
        $this->assertObjectHasAttribute($item->type, $item);
        $this->assertEquals('keywords', $item->{$item->type});
        $this->assertEquals($string, $item->content);
    }

    public function testOverloadingAppendNameAppendsMetaTagToStack()
    {
        $this->_testOverloadAppend('name');
    }

    public function testOverloadingPrependNamePrependsMetaTagToStack()
    {
        $this->_testOverloadPrepend('name');
    }

    public function testOverloadingSetNameOverwritesMetaTagStack()
    {
        $this->_testOverloadSet('name');
    }

    public function testOverloadingAppendHttpEquivAppendsMetaTagToStack()
    {
        $this->_testOverloadAppend('http-equiv');
    }

    public function testOverloadingPrependHttpEquivPrependsMetaTagToStack()
    {
        $this->_testOverloadPrepend('http-equiv');
    }

    public function testOverloadingSetHttpEquivOverwritesMetaTagStack()
    {
        $this->_testOverloadSet('http-equiv');
    }

    public function testOverloadingThrowsExceptionWithFewerThanTwoArgs()
    {
        $this->setExpectedException('Zend\View\Exception\ExceptionInterface');
        $this->helper->setName('foo');
    }

    public function testOverloadingThrowsExceptionWithInvalidMethodType()
    {
        $this->setExpectedException('Zend\View\Exception\ExceptionInterface');
        $this->helper->setFoo('foo');
    }

    public function testCanBuildMetaTagsWithAttributes()
    {
        $this->helper->setName('keywords', 'foo bar', array('lang' => 'us_en', 'scheme' => 'foo', 'bogus' => 'unused'));
        $value = $this->helper->getValue();

        $this->assertObjectHasAttribute('modifiers', $value);
        $modifiers = $value->modifiers;
        $this->assertTrue(array_key_exists('lang', $modifiers));
        $this->assertEquals('us_en', $modifiers['lang']);
        $this->assertTrue(array_key_exists('scheme', $modifiers));
        $this->assertEquals('foo', $modifiers['scheme']);
    }

    public function testToStringReturnsValidHtml()
    {
        $this->helper->setName('keywords', 'foo bar', array('lang' => 'us_en', 'scheme' => 'foo', 'bogus' => 'unused'))
                     ->prependName('title', 'boo bah')
                     ->appendHttpEquiv('screen', 'projection');
        $string = $this->helper->toString();

        $metas = substr_count($string, '<meta ');
        $this->assertEquals(3, $metas);
        $metas = substr_count($string, '/>');
        $this->assertEquals(3, $metas);
        $metas = substr_count($string, 'name="');
        $this->assertEquals(2, $metas);
        $metas = substr_count($string, 'http-equiv="');
        $this->assertEquals(1, $metas);

        $this->assertContains('http-equiv="screen" content="projection"', $string);
        $this->assertContains('name="keywords" content="foo bar"', $string);
        $this->assertContains('lang="us_en"', $string);
        $this->assertContains('scheme="foo"', $string);
        $this->assertNotContains('bogus', $string);
        $this->assertNotContains('unused', $string);
        $this->assertContains('name="title" content="boo bah"', $string);
    }

    /**
     * @group ZF-6637
     */
    public function testToStringWhenInvalidKeyProvidedShouldConvertThrownException()
    {
        $this->helper->__invoke('some-content', 'tag value', 'not allowed key');
        set_error_handler(array($this, 'handleErrors'));
        $string = @$this->helper->toString();
        $this->assertEquals('', $string);
        $this->assertTrue(is_string($this->error));
    }

    public function testHeadMetaHelperCreatesItemEntry()
    {
        $this->helper->__invoke('foo', 'keywords');
        $values = $this->helper->getArrayCopy();
        $this->assertEquals(1, count($values));
        $item = array_shift($values);
        $this->assertEquals('foo', $item->content);
        $this->assertEquals('name', $item->type);
        $this->assertEquals('keywords', $item->name);
    }

    public function testOverloadingOffsetInsertsAtOffset()
    {
        $this->helper->offsetSetName(100, 'keywords', 'foo');
        $values = $this->helper->getArrayCopy();
        $this->assertEquals(1, count($values));
        $this->assertTrue(array_key_exists(100, $values));
        $item = $values[100];
        $this->assertEquals('foo', $item->content);
        $this->assertEquals('name', $item->type);
        $this->assertEquals('keywords', $item->name);
    }

    public function testIndentationIsHonored()
    {
        $this->helper->setIndent(4);
        $this->helper->appendName('keywords', 'foo bar');
        $this->helper->appendName('seo', 'baz bat');
        $string = $this->helper->toString();

        $scripts = substr_count($string, '    <meta name=');
        $this->assertEquals(2, $scripts);
    }

    public function testStringRepresentationReflectsDoctype()
    {
        $this->view->plugin('doctype')->__invoke('HTML4_STRICT');
        $this->helper->__invoke('some content', 'foo');
        $test = $this->helper->toString();
        $this->assertNotContains('/>', $test);
        $this->assertContains('some content', $test);
        $this->assertContains('foo', $test);
    }

    /**
     * @issue ZF-2663
     */
    public function testSetNameDoesntClobber()
    {
        $view = new View();
        $view->plugin('headMeta')->setName('keywords', 'foo');
        $view->plugin('headMeta')->appendHttpEquiv('pragma', 'bar');
        $view->plugin('headMeta')->appendHttpEquiv('Cache-control', 'baz');
        $view->plugin('headMeta')->setName('keywords', 'bat');

        $this->assertEquals(
            '<meta http-equiv="pragma" content="bar" />' . PHP_EOL . '<meta http-equiv="Cache-control" content="baz" />' . PHP_EOL . '<meta name="keywords" content="bat" />',
            $view->plugin('headMeta')->toString()
            );
    }

    /**
     * @issue ZF-2663
     */
    public function testSetNameDoesntClobberPart2()
    {
        $view = new View();
        $view->plugin('headMeta')->setName('keywords', 'foo');
        $view->plugin('headMeta')->setName('description', 'foo');
        $view->plugin('headMeta')->appendHttpEquiv('pragma', 'baz');
        $view->plugin('headMeta')->appendHttpEquiv('Cache-control', 'baz');
        $view->plugin('headMeta')->setName('keywords', 'bar');

        $this->assertEquals(
            '<meta name="description" content="foo" />' . PHP_EOL . '<meta http-equiv="pragma" content="baz" />' . PHP_EOL . '<meta http-equiv="Cache-control" content="baz" />' . PHP_EOL . '<meta name="keywords" content="bar" />',
            $view->plugin('headMeta')->toString()
            );
    }

    /**
     * @issue ZF-3780
     * @link http://framework.zend.com/issues/browse/ZF-3780
     */
    public function testPlacesMetaTagsInProperOrder()
    {
        $view = new View();
        $view->plugin('headMeta')->setName('keywords', 'foo');
        $view->plugin('headMeta')->__invoke('some content', 'bar', 'name', array(), \Zend\View\Helper\Placeholder\Container\AbstractContainer::PREPEND);

        $this->assertEquals(
            '<meta name="bar" content="some content" />' . PHP_EOL . '<meta name="keywords" content="foo" />',
            $view->plugin('headMeta')->toString()
            );
    }

    /**
     * @issue ZF-5435
     */
    public function testContainerMaintainsCorrectOrderOfItems()
    {

        $this->helper->offsetSetName(1, 'keywords', 'foo');
        $this->helper->offsetSetName(10, 'description', 'foo');
        $this->helper->offsetSetHttpEquiv(20, 'pragma', 'baz');
        $this->helper->offsetSetHttpEquiv(5, 'Cache-control', 'baz');

        $test = $this->helper->toString();

        $expected = '<meta name="keywords" content="foo" />' . PHP_EOL
                  . '<meta http-equiv="Cache-control" content="baz" />' . PHP_EOL
                  . '<meta name="description" content="foo" />' . PHP_EOL
                  . '<meta http-equiv="pragma" content="baz" />';

        $this->assertEquals($expected, $test);
    }

    /**
     * @issue ZF-7722
     */
    public function testCharsetValidateFail()
    {
        $view = new View();
        $view->plugin('doctype')->__invoke('HTML4_STRICT');

        $this->setExpectedException('Zend\View\Exception\ExceptionInterface');
        $view->plugin('headMeta')->setCharset('utf-8');
    }

    /**
     * @issue ZF-7722
     */
    public function testCharset()
    {
        $view = new View();
        $view->plugin('doctype')->__invoke('HTML5');

        $view->plugin('headMeta')->setCharset('utf-8');
        $this->assertEquals(
            '<meta charset="utf-8">',
            $view->plugin('headMeta')->toString());

        $view->plugin('doctype')->__invoke('XHTML5');

        $this->assertEquals(
            '<meta charset="utf-8"/>',
            $view->plugin('headMeta')->toString());
    }

     /**
     * @group ZF-9743
     */
    public function testPropertyIsSupportedWithRdfaDoctype()
    {
        $this->view->doctype('XHTML1_RDFA');
        $this->helper->__invoke('foo', 'og:title', 'property');
        $this->assertEquals('<meta property="og:title" content="foo" />',
                            $this->helper->toString()
                           );
    }

    /**
     * @group ZF-9743
     */
    public function testPropertyIsNotSupportedByDefaultDoctype()
    {
        try {
            $this->helper->__invoke('foo', 'og:title', 'property');
            $this->fail('meta property attribute should not be supported on default doctype');
        } catch (ViewException $e) {
            $this->assertContains('Invalid value passed', $e->getMessage());
        }
    }

    /**
     * @group ZF-9743
     * @depends testPropertyIsSupportedWithRdfaDoctype
     */
    public function testOverloadingAppendPropertyAppendsMetaTagToStack()
    {
        $this->view->doctype('XHTML1_RDFA');
        $this->_testOverloadAppend('property');
    }

    /**
     * @group ZF-9743
     * @depends testPropertyIsSupportedWithRdfaDoctype
     */
    public function testOverloadingPrependPropertyPrependsMetaTagToStack()
    {
        $this->view->doctype('XHTML1_RDFA');
        $this->_testOverloadPrepend('property');
    }

    /**
     * @group ZF-9743
     * @depends testPropertyIsSupportedWithRdfaDoctype
     */
    public function testOverloadingSetPropertyOverwritesMetaTagStack()
    {
        $this->view->doctype('XHTML1_RDFA');
        $this->_testOverloadSet('property');
    }

    /**
     * @group ZF-11835
     */
    public function testConditional()
    {
        $html = $this->helper->appendHttpEquiv('foo', 'bar', array('conditional' => 'lt IE 7'))->toString();

        $this->assertRegExp("|^<!--\[if lt IE 7\]>|", $html);
        $this->assertRegExp("|<!\[endif\]-->$|", $html);
    }

}
