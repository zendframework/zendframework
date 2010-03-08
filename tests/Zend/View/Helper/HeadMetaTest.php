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

// Call Zend_View_Helper_HeadMetaTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HeadMetaTest::main");
}


/** Zend_View_Helper_HeadMeta */

/** Zend_View_Helper_Placeholder_Registry */

/** Zend_Registry */

/** Zend_View */

/**
 * Test class for Zend_View_Helper_HeadMeta.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_HeadMetaTest extends PHPUnit_Framework_TestCase
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
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HeadMetaTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->error = false;
        foreach (array(Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY, 'Zend_View_Helper_Doctype') as $key) {
            if (Zend_Registry::isRegistered($key)) {
                $registry = Zend_Registry::getInstance();
                unset($registry[$key]);
            }
        }
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->view     = new Zend_View();
        $this->view->doctype('XHTML1_STRICT');
        $this->helper   = new Zend_View_Helper_HeadMeta();
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
        $registry = Zend_View_Helper_Placeholder_Registry::getRegistry();
        if ($registry->containerExists('Zend_View_Helper_HeadMeta')) {
            $registry->deleteContainer('Zend_View_Helper_HeadMeta');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_HeadMeta'));
        $helper = new Zend_View_Helper_HeadMeta();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_HeadMeta'));
    }

    public function testHeadMetaReturnsObjectInstance()
    {
        $placeholder = $this->helper->headMeta();
        $this->assertTrue($placeholder instanceof Zend_View_Helper_HeadMeta);
    }

    public function testAppendPrependAndSetThrowExceptionsWhenNonMetaValueProvided()
    {
        try {
            $this->helper->append('foo');
            $this->fail('Non-meta value should not append');
        } catch (Zend_View_Exception $e) {
        }
        try {
            $this->helper->offsetSet(3, 'foo');
            $this->fail('Non-meta value should not offsetSet');
        } catch (Zend_View_Exception $e) {
        }
        try {
            $this->helper->prepend('foo');
            $this->fail('Non-meta value should not prepend');
        } catch (Zend_View_Exception $e) {
        }
        try {
            $this->helper->set('foo');
            $this->fail('Non-meta value should not set');
        } catch (Zend_View_Exception $e) {
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
        try {
            $this->helper->setName('foo');
            $this->fail('Overloading should require at least two arguments');
        } catch (Zend_View_Exception $e) {
        }
    }

    public function testOverloadingThrowsExceptionWithInvalidMethodType()
    {
        try {
            $this->helper->setFoo('foo');
            $this->fail('Overloading should only work for (set|prepend|append)(Name|HttpEquiv)');
        } catch (Zend_View_Exception $e) {
        }
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
        $this->helper->headMeta('some-content', 'tag value', 'not allowed key');
        set_error_handler(array($this, 'handleErrors'));
        $string = @$this->helper->toString();
        $this->assertEquals('', $string);
        $this->assertTrue(is_string($this->error));
    }

    public function testHeadMetaHelperCreatesItemEntry()
    {
        $this->helper->headMeta('foo', 'keywords');
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
        $this->view->doctype('HTML4_STRICT');
        $this->helper->headMeta('some content', 'foo');
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
        $view = new Zend_View();
        $view->headMeta()->setName('keywords', 'foo');
        $view->headMeta()->appendHttpEquiv('pragma', 'bar');
        $view->headMeta()->appendHttpEquiv('Cache-control', 'baz');
        $view->headMeta()->setName('keywords', 'bat');

        $this->assertEquals(
            '<meta http-equiv="pragma" content="bar" />' . PHP_EOL . '<meta http-equiv="Cache-control" content="baz" />' . PHP_EOL . '<meta name="keywords" content="bat" />',
            $view->headMeta()->toString()
            );
    }

    /**
     * @issue ZF-2663
     */
    public function testSetNameDoesntClobberPart2()
    {
        $view = new Zend_View();
        $view->headMeta()->setName('keywords', 'foo');
        $view->headMeta()->setName('description', 'foo');
        $view->headMeta()->appendHttpEquiv('pragma', 'baz');
        $view->headMeta()->appendHttpEquiv('Cache-control', 'baz');
        $view->headMeta()->setName('keywords', 'bar');

        $this->assertEquals(
            '<meta name="description" content="foo" />' . PHP_EOL . '<meta http-equiv="pragma" content="baz" />' . PHP_EOL . '<meta http-equiv="Cache-control" content="baz" />' . PHP_EOL . '<meta name="keywords" content="bar" />',
            $view->headMeta()->toString()
            );
    }

    /**
     * @issue ZF-3780
     * @link http://framework.zend.com/issues/browse/ZF-3780
     */
    public function testPlacesMetaTagsInProperOrder()
    {
        $view = new Zend_View();
        $view->headMeta()->setName('keywords', 'foo');
        $view->headMeta('some content', 'bar', 'name', array(), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);

        $this->assertEquals(
            '<meta name="bar" content="some content" />' . PHP_EOL . '<meta name="keywords" content="foo" />',
            $view->headMeta()->toString()
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
		$view = new Zend_View();
		$view->doctype('HTML4_STRICT');

		try {
			$view->headMeta()->setCharset('utf-8');
			$this->fail('Should not be able to set charset for a HTML4 doctype');
		} catch (Zend_View_Exception $e) {}
	}

	/**
	 * @issue ZF-7722
	 */
	public function testCharset() {
		$view = new Zend_View();
		$view->doctype('HTML5');

		$view->headMeta()->setCharset('utf-8');
		$this->assertEquals(
			'<meta charset="utf-8">',
			$view->headMeta()->toString());

		$view->doctype('XHTML5');

		$this->assertEquals(
			'<meta charset="utf-8"/>',
			$view->headMeta()->toString());
	}

}

// Call Zend_View_Helper_HeadMetaTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HeadMetaTest::main") {
    Zend_View_Helper_HeadMetaTest::main();
}
