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
 * @package    Zend_GData_OpenSearch
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData;
use Zend\GData\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_OpenSearch
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_OpenSearch
 */
class OpenSearchStartIndexTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->openSearchStartIndexText = file_get_contents(
                'Zend/GData/_files/OpenSearchStartIndexElementSample1.xml',
                true);
        $this->openSearchStartIndex = new Extension\OpenSearchStartIndex();
    }

    public function testEmptyOpenSearchStartIndexShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->openSearchStartIndex->extensionElements));
        $this->assertTrue(count($this->openSearchStartIndex->extensionElements) == 0);
    }

    public function testEmptyOpenSearchStartIndexShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->openSearchStartIndex->extensionAttributes));
        $this->assertTrue(count($this->openSearchStartIndex->extensionAttributes) == 0);
    }

    public function testSampleOpenSearchStartIndexShouldHaveNoExtensionElements() {
        $this->openSearchStartIndex->transferFromXML($this->openSearchStartIndexText);
        $this->assertTrue(is_array($this->openSearchStartIndex->extensionElements));
        $this->assertTrue(count($this->openSearchStartIndex->extensionElements) == 0);
    }

    public function testSampleOpenSearchStartIndexShouldHaveNoExtensionAttributes() {
        $this->openSearchStartIndex->transferFromXML($this->openSearchStartIndexText);
        $this->assertTrue(is_array($this->openSearchStartIndex->extensionAttributes));
        $this->assertTrue(count($this->openSearchStartIndex->extensionAttributes) == 0);
    }

    public function testNormalOpenSearchStartIndexShouldHaveNoExtensionElements() {
        $this->openSearchStartIndex->text = "20";

        $this->assertEquals("20", $this->openSearchStartIndex->text);

        $this->assertEquals(0, count($this->openSearchStartIndex->extensionElements));
        $newOpenSearchStartIndex = new Extension\OpenSearchStartIndex();
        $newOpenSearchStartIndex->transferFromXML($this->openSearchStartIndex->saveXML());
        $this->assertEquals(0, count($newOpenSearchStartIndex->extensionElements));
        $newOpenSearchStartIndex->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newOpenSearchStartIndex->extensionElements));
        $this->assertEquals("20", $newOpenSearchStartIndex->text);

        /* try constructing using magic factory */
        $gdata = new \Zend\GData\GData();
        $newOpenSearchStartIndex2 = $gdata->newOpenSearchStartIndex();
        $newOpenSearchStartIndex2->transferFromXML($newOpenSearchStartIndex->saveXML());
        $this->assertEquals(1, count($newOpenSearchStartIndex2->extensionElements));
        $this->assertEquals("20", $newOpenSearchStartIndex2->text);
    }

    public function testEmptyOpenSearchStartIndexToAndFromStringShouldMatch() {
        $openSearchStartIndexXml = $this->openSearchStartIndex->saveXML();
        $newOpenSearchStartIndex = new Extension\OpenSearchStartIndex();
        $newOpenSearchStartIndex->transferFromXML($openSearchStartIndexXml);
        $newOpenSearchStartIndexXml = $newOpenSearchStartIndex->saveXML();
        $this->assertTrue($openSearchStartIndexXml == $newOpenSearchStartIndexXml);
    }

    public function testOpenSearchStartIndexWithValueToAndFromStringShouldMatch() {
        $this->openSearchStartIndex->text = "20";
        $openSearchStartIndexXml = $this->openSearchStartIndex->saveXML();
        $newOpenSearchStartIndex = new Extension\OpenSearchStartIndex();
        $newOpenSearchStartIndex->transferFromXML($openSearchStartIndexXml);
        $newOpenSearchStartIndexXml = $newOpenSearchStartIndex->saveXML();
        $this->assertTrue($openSearchStartIndexXml == $newOpenSearchStartIndexXml);
        $this->assertEquals("20", $this->openSearchStartIndex->text);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->openSearchStartIndex->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->openSearchStartIndex->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->openSearchStartIndex->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->openSearchStartIndex->extensionAttributes['foo2']['value']);
        $openSearchStartIndexXml = $this->openSearchStartIndex->saveXML();
        $newOpenSearchStartIndex = new Extension\OpenSearchStartIndex();
        $newOpenSearchStartIndex->transferFromXML($openSearchStartIndexXml);
        $this->assertEquals('bar', $newOpenSearchStartIndex->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newOpenSearchStartIndex->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullOpenSearchStartIndexToAndFromString() {
        $this->openSearchStartIndex->transferFromXML($this->openSearchStartIndexText);
        $this->assertEquals("5", $this->openSearchStartIndex->text);
    }

}
