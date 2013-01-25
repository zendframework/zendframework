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

use Zend\View\Helper;

/**
 * Test class for Zend_View_Helper_Doctype.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class DoctypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Helper\Doctype
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
        Helper\Doctype::unsetDoctypeRegistry();
        $this->helper = new Helper\Doctype();
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

    public function testDoctypeMethodReturnsObjectInstance()
    {
        $doctype = $this->helper->__invoke();
        $this->assertTrue($doctype instanceof Helper\Doctype);
    }

    public function testPassingDoctypeSetsDoctype()
    {
        $doctype = $this->helper->__invoke(Helper\Doctype::XHTML1_STRICT);
        $this->assertEquals(Helper\Doctype::XHTML1_STRICT, $doctype->getDoctype());
    }

    public function testIsXhtmlReturnsTrueForXhtmlDoctypes()
    {
        $types = array(
            Helper\Doctype::XHTML1_STRICT,
            Helper\Doctype::XHTML1_TRANSITIONAL,
            Helper\Doctype::XHTML1_FRAMESET,
            Helper\Doctype::XHTML1_RDFA,
            Helper\Doctype::XHTML1_RDFA11,
            Helper\Doctype::XHTML5
        );

        foreach ($types as $type) {
            $doctype = $this->helper->__invoke($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertTrue($doctype->isXhtml());
        }

        $doctype = $this->helper->__invoke('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://framework.zend.com/foo/DTD/xhtml1-custom.dtd">');
        $this->assertEquals('CUSTOM_XHTML', $doctype->getDoctype());
        $this->assertTrue($doctype->isXhtml());
    }

    public function testIsXhtmlReturnsFalseForNonXhtmlDoctypes()
    {
        $types = array(
            Helper\Doctype::HTML4_STRICT,
            Helper\Doctype::HTML4_LOOSE,
            Helper\Doctype::HTML4_FRAMESET,
        );

        foreach ($types as $type) {
            $doctype = $this->helper->__invoke($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertFalse($doctype->isXhtml());
        }

        $doctype = $this->helper->__invoke('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 10.0 Strict//EN" "http://framework.zend.com/foo/DTD/html10-custom.dtd">');
        $this->assertEquals('CUSTOM', $doctype->getDoctype());
        $this->assertFalse($doctype->isXhtml());
    }

    public function testIsHtml5()
    {
        foreach (array(Helper\Doctype::HTML5, Helper\Doctype::XHTML5) as $type) {
            $doctype = $this->helper->__invoke($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertTrue($doctype->isHtml5());
        }

        $types = array(
            Helper\Doctype::HTML4_STRICT,
            Helper\Doctype::HTML4_LOOSE,
            Helper\Doctype::HTML4_FRAMESET,
            Helper\Doctype::XHTML1_STRICT,
            Helper\Doctype::XHTML1_TRANSITIONAL,
            Helper\Doctype::XHTML1_FRAMESET
        );


        foreach ($types as $type) {
            $doctype = $this->helper->__invoke($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertFalse($doctype->isHtml5());
        }
    }

    public function testIsRdfa()
    {
        // ensure default registerd Doctype is false
        $this->assertFalse($this->helper->isRdfa());

        $this->assertTrue($this->helper->__invoke(Helper\Doctype::XHTML1_RDFA)->isRdfa());
        $this->assertTrue($this->helper->__invoke(Helper\Doctype::XHTML1_RDFA11)->isRdfa());
        $this->assertTrue($this->helper->__invoke(Helper\Doctype::XHTML5)->isRdfa());
        $this->assertTrue($this->helper->__invoke(Helper\Doctype::HTML5)->isRdfa());

        // build-in doctypes
        $doctypes = array(
            Helper\Doctype::XHTML11,
            Helper\Doctype::XHTML1_STRICT,
            Helper\Doctype::XHTML1_TRANSITIONAL,
            Helper\Doctype::XHTML1_FRAMESET,
            Helper\Doctype::XHTML_BASIC1,
            Helper\Doctype::HTML4_STRICT,
            Helper\Doctype::HTML4_LOOSE,
            Helper\Doctype::HTML4_FRAMESET,
        );

        foreach ($doctypes as $type) {
            $this->assertFalse($this->helper->__invoke($type)->isRdfa());
        }

        // custom doctype
        $doctype = $this->helper->__invoke('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 10.0 Strict//EN" "http://framework.zend.com/foo/DTD/html10-custom.dtd">');
        $this->assertFalse($doctype->isRdfa());
    }

    public function testCanRegisterCustomHtml5Doctype()
    {
        $doctype = $this->helper->__invoke('<!DOCTYPE html>');
        $this->assertEquals('CUSTOM', $doctype->getDoctype());
        $this->assertTrue($doctype->isHtml5());
    }

    public function testCanRegisterCustomXhtmlDoctype()
    {
        $doctype = $this->helper->__invoke('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://framework.zend.com/foo/DTD/xhtml1-custom.dtd">');
        $this->assertEquals('CUSTOM_XHTML', $doctype->getDoctype());
        $this->assertTrue($doctype->isXhtml());
    }

    public function testCanRegisterCustomHtmlDoctype()
    {
        $doctype = $this->helper->__invoke('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 10.0 Strict//EN" "http://framework.zend.com/foo/DTD/html10-custom.dtd">');
        $this->assertEquals('CUSTOM', $doctype->getDoctype());
        $this->assertFalse($doctype->isXhtml());
    }

    public function testMalformedCustomDoctypeRaisesException()
    {
        try {
            $doctype = $this->helper->__invoke('<!FOO HTML>');
            $this->fail('Malformed doctype should raise exception');
        } catch (\Exception $e) {
        }
    }

    public function testStringificationReturnsDoctypeString()
    {
        $doctype = $this->helper->__invoke(Helper\Doctype::XHTML1_STRICT);
        $string   = $doctype->__toString();
        $this->assertEquals('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">', $string);
    }
}
