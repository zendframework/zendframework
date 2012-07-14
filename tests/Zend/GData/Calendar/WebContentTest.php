<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\Calendar;

use Zend\GData\Calendar\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Calendar
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Calendar
 */
class WebContentTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->webContentText = file_get_contents(
                'Zend/GData/Calendar/_files/WebContentElementSample1.xml',
                true);
        $this->webContent = new Extension\WebContent();
    }

    public function testEmptyWebContentShouldHaveNoExtensionElements()
    {
        $this->assertTrue(is_array($this->webContent->extensionElements));
        $this->assertTrue(count($this->webContent->extensionElements) == 0);
    }

    public function testEmptyWebContentShouldHaveNoExtensionAttributes()
    {
        $this->assertTrue(is_array($this->webContent->extensionAttributes));
        $this->assertTrue(count($this->webContent->extensionAttributes) == 0);
    }

    public function testSampleWebContentShouldHaveNoExtensionElements()
    {
        $this->webContent->transferFromXML($this->webContentText);
        $this->assertTrue(is_array($this->webContent->extensionElements));
        $this->assertTrue(count($this->webContent->extensionElements) == 0);
    }

    public function testSampleWebContentShouldHaveNoExtensionAttributes()
    {
        $this->webContent->transferFromXML($this->webContentText);
        $this->assertTrue(is_array($this->webContent->extensionAttributes));
        $this->assertTrue(count($this->webContent->extensionAttributes) == 0);
    }

    public function testNormalWebContentShouldHaveNoExtensionElements()
    {
        $this->webContent->url = "http://nowhere.invalid/";
        $this->webContent->height = "100";
        $this->webContent->width = "200";

        $this->assertEquals($this->webContent->url, "http://nowhere.invalid/");
        $this->assertEquals($this->webContent->height, "100");
        $this->assertEquals($this->webContent->width, "200");

        $this->assertEquals(count($this->webContent->extensionElements), 0);
        $newWebContent = new Extension\WebContent();
        $newWebContent->transferFromXML($this->webContent->saveXML());
        $this->assertEquals(count($newWebContent->extensionElements), 0);
        $newWebContent->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newWebContent->extensionElements), 1);
        $this->assertEquals($newWebContent->url, "http://nowhere.invalid/");
        $this->assertEquals($newWebContent->height, "100");
        $this->assertEquals($newWebContent->width, "200");

        /* try constructing using magic factory */
        $cal = new \Zend\GData\Calendar();
        $newWebContent2 = $cal->newWebContent();
        $newWebContent2->transferFromXML($newWebContent->saveXML());
        $this->assertEquals(count($newWebContent2->extensionElements), 1);
        $this->assertEquals($newWebContent2->url, "http://nowhere.invalid/");
        $this->assertEquals($newWebContent2->height, "100");
        $this->assertEquals($newWebContent2->width, "200");
    }

    public function testEmptyWebContentToAndFromStringShouldMatch()
    {
        $webContentXml = $this->webContent->saveXML();
        $newWebContent = new Extension\WebContent();
        $newWebContent->transferFromXML($webContentXml);
        $newWebContentXml = $newWebContent->saveXML();
        $this->assertTrue($webContentXml == $newWebContentXml);
    }

    public function testWebContentWithValueToAndFromStringShouldMatch()
    {
        $this->webContent->url = "http://nowhere.invalid/";
        $this->webContent->height = "100";
        $this->webContent->width = "200";
        $webContentXml = $this->webContent->saveXML();
        $newWebContent = new Extension\WebContent();
        $newWebContent->transferFromXML($webContentXml);
        $newWebContentXml = $newWebContent->saveXML();
        $this->assertTrue($webContentXml == $newWebContentXml);
        $this->assertEquals($this->webContent->url, "http://nowhere.invalid/");
        $this->assertEquals($this->webContent->height, "100");
        $this->assertEquals($this->webContent->width, "200");
    }

    public function testExtensionAttributes()
    {
        $extensionAttributes = $this->webContent->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->webContent->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->webContent->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->webContent->extensionAttributes['foo2']['value']);
        $webContentXml = $this->webContent->saveXML();
        $newWebContent = new Extension\WebContent();
        $newWebContent->transferFromXML($webContentXml);
        $this->assertEquals('bar', $newWebContent->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newWebContent->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullWebContentToAndFromString()
    {
        $this->webContent->transferFromXML($this->webContentText);
        $this->assertEquals($this->webContent->url, "http://www.google.com/logos/july4th06.gif");
        $this->assertEquals($this->webContent->height, "120");
        $this->assertEquals($this->webContent->width, "276");
    }

}
