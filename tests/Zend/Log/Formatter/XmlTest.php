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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Formatter;

use ZendTest\Log\TestAsset\SerializableObject;
use Zend\Log\Formatter\Xml as XmlFormatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultFormat()
    {
        $f = new XmlFormatter();
        $line = $f->format(array('message' => 'foo', 'priority' => 42));

        $this->assertContains('foo', $line);
        $this->assertContains((string)42, $line);
    }

    public function testConfiguringElementMapping()
    {
        $f = new XmlFormatter('log', array('foo' => 'bar'));
        $line = $f->format(array('bar' => 'baz'));
        $this->assertContains('<log><foo>baz</foo></log>', $line);
    }

    public function testXmlDeclarationIsStripped()
    {
        $f = new XmlFormatter();
        $line = $f->format(array('message' => 'foo', 'priority' => 42));

        $this->assertNotContains('<\?xml version=', $line);
    }

    public function testXmlValidates()
    {
        $f = new XmlFormatter();
        $line = $f->format(array('message' => 'foo', 'priority' => 42));

        $sxml = @simplexml_load_string($line);
        $this->assertInstanceOf('SimpleXMLElement', $sxml, 'Formatted XML is invalid');
    }

    /**
     * @group ZF-2062
     * @group ZF-4190
     */
    public function testHtmlSpecialCharsInMessageGetEscapedForValidXml()
    {
        $f = new XmlFormatter();
        $line = $f->format(array('message' => '&key1=value1&key2=value2', 'priority' => 42));

        $this->assertContains("&amp;", $line);
        $this->assertTrue(substr_count($line, "&amp;") == 2);
    }

    /**
     * @group ZF-2062
     * @group ZF-4190
     */
    public function testFixingBrokenCharsSoXmlIsValid()
    {
        $f = new XmlFormatter();
        $line = $f->format(array('message' => '&amp', 'priority' => 42));

        $this->assertContains('&amp;amp', $line);
    }

    public function testConstructorWithArray()
    {
        $options = array(
            'rootElement' => 'log',
            'elementMap' => array(
                'word' => 'message',
                'priority' => 'priority'
            )
        );
        $event = array(
            'message' => 'tottakai',
            'priority' => 4
        );
        $expected = '<log><word>tottakai</word><priority>4</priority></log>';

        $formatter = new XmlFormatter($options);
        $output = $formatter->format($event);
        $this->assertContains($expected, $output);
        $this->assertEquals('UTF-8', $formatter->getEncoding());
    }

    /**
     * @group ZF-11161
     */
    public function testNonScalarValuesAreExcludedFromFormattedString()
    {
        $options = array(
            'rootElement' => 'log'
        );
        $event = array(
            'message' => 'tottakai',
            'priority' => 4,
            'context' => array('test'=>'one'),
            'reference' => new XmlFormatter()
        );
        $expected = '<log><message>tottakai</message><priority>4</priority></log>';

        $formatter = new XmlFormatter($options);
        $output = $formatter->format($event);
        $this->assertContains($expected, $output);
    }

    /**
     * @group ZF-11161
     */
    public function testObjectsWithStringSerializationAreIncludedInFormattedString()
    {
        $options = array(
            'rootElement' => 'log'
        );
        $event = array(
            'message' => 'tottakai',
            'priority' => 4,
            'context' => array('test'=>'one'),
            'reference' => new SerializableObject()
        );
        $expected = '<log><message>tottakai</message><priority>4</priority><reference>ZendTest\Log\TestAsset\SerializableObject</reference></log>';

        $formatter = new XmlFormatter($options);
        $output = $formatter->format($event);
        $this->assertContains($expected, $output);
    }
}
