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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Amf\Adobe;

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class IntrospectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->introspector = new \Zend\Amf\Adobe\Introspector();
    }

    public function testIntrospectionDoesNotIncludeConstructor()
    {
        $xml = $this->introspector->introspect('ZendTest.Amf.TestAsset.IntrospectorTest');
        $this->assertNotContains('__construct', $xml);
    }

    public function testIntrospectionDoesNotIncludeMagicMethods()
    {
        $xml = $this->introspector->introspect('ZendTest.Amf.TestAsset.IntrospectorTest');
        $this->assertNotContains('__get', $xml);
    }

    public function testIntrospectionContainsPublicPropertiesOfReturnClassTypes()
    {
        $xml = $this->introspector->introspect('ZendTest.Amf.TestAsset.IntrospectorTest');
        $this->assertRegexp('/<type[^>]*(name="' . preg_quote('ZendTest\\Amf\\TestAsset\\IntrospectorTestCustomType') . '")/', $xml, $xml);
        $this->assertRegexp('/<property[^>]*(name="foo")/', $xml, $xml);
        $this->assertRegexp('/<property[^>]*(type="string")/', $xml, $xml);
    }

    public function testIntrospectionDoesNotContainNonPublicPropertiesOfReturnClassTypes()
    {
        $xml = $this->introspector->introspect('ZendTest.Amf.TestAsset.IntrospectorTest');
        $this->assertNotRegexp('/<property[^>]*(name="_bar")/', $xml, $xml);
    }

    public function testIntrospectionContainsPublicMethods()
    {
        $xml = $this->introspector->introspect('ZendTest.Amf.TestAsset.IntrospectorTest');
        $this->assertRegexp('/<operation[^>]*(name="foobar")/', $xml, $xml);
        $this->assertRegexp('/<operation[^>]*(name="barbaz")/', $xml, $xml);
        $this->assertRegexp('/<operation[^>]*(name="bazbat")/', $xml, $xml);
    }

    public function testIntrospectionContainsOperationForEachPrototypeOfAPublicMethod()
    {
        $xml = $this->introspector->introspect('ZendTest.Amf.TestAsset.IntrospectorTest');
        $this->assertEquals(4, substr_count($xml, 'name="foobar"'));
        $this->assertEquals(1, substr_count($xml, 'name="barbaz"'));
        $this->assertEquals(1, substr_count($xml, 'name="bazbat"'));
    }

    public function testPassingDirectoriesOptionShouldResolveServiceClassAndType()
    {
        $xml = $this->introspector->introspect('ZendAmfAdobeIntrospectorTest', array(
            'directories' => array(__DIR__ . '/../TestAsset'),
        ));
        $this->assertRegexp('/<operation[^>]*(name="foo")/', $xml, $xml);
        $this->assertRegexp('/<type[^>]*(name="ZendAmfAdobeIntrospectorTestType")/', $xml, $xml);
        $this->assertRegexp('/<property[^>]*(name="bar")/', $xml, $xml);
    }

    public function testMissingPropertyDocblockInTypedClassShouldReportTypeAsUnknown()
    {
        $xml = $this->introspector->introspect('ZendTest.Amf.TestAsset.IntrospectorTest');
        if (!preg_match('/(<property[^>]*(name="baz")[^>]*>)/', $xml, $matches)) {
            $this->fail('Baz property of ZendTest.Amf.TestAsset.IntrospectorTestCustomType not found');
        }
        $node = $matches[1];
        $this->assertContains('type="Unknown"', $node, $node);
    }

    public function testPropertyDocblockWithoutAnnotationInTypedClassShouldReportTypeAsUnknown()
    {
        $xml = $this->introspector->introspect('ZendTest.Amf.TestAsset.IntrospectorTest');
        if (!preg_match('/(<property[^>]*(name="bat")[^>]*>)/', $xml, $matches)) {
            $this->fail('Bat property of ZendTest.Amf.TestAsset.IntrospectorTestCustomType not found');
        }
        $node = $matches[1];
        $this->assertContains('type="Unknown"', $node, $node);
    }

    public function testTypedClassWithExplicitTypeShouldReportAsThatType()
    {
        $xml = $this->introspector->introspect('ZendTest.Amf.TestAsset.IntrospectorTest');
        $this->assertRegexp('/<type[^>]*(name="explicit")/', $xml, $xml);
    }
}


