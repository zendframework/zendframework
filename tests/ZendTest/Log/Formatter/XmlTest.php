<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Formatter;

use DateTime;
use ZendTest\Log\TestAsset\SerializableObject;
use Zend\Log\Formatter\Xml as XmlFormatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultFormat()
    {
        $date = new DateTime();
        $f = new XmlFormatter();
        $line = $f->format(array('timestamp' => $date, 'message' => 'foo', 'priority' => 42));

        $this->assertContains($date->format('c'), $line);
        $this->assertContains('foo', $line);
        $this->assertContains((string)42, $line);
    }

    public function testConfiguringElementMapping()
    {
        $f = new XmlFormatter('log', array('foo' => 'bar'));
        $line = $f->format(array('bar' => 'baz'));
        $this->assertContains('<log><foo>baz</foo></log>', $line);
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testConfiguringDateTimeFormat($dateTimeFormat)
    {
        $date = new DateTime();
        $f = new XmlFormatter('log', null, 'UTF-8', $dateTimeFormat);
        $this->assertContains($date->format($dateTimeFormat), $f->format(array('timestamp' => $date)));
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testSetDateTimeFormat($dateTimeFormat)
    {
        $date = new DateTime();
        $f = new XmlFormatter();
        $this->assertSame($f, $f->setDateTimeFormat($dateTimeFormat));
        $this->assertContains($dateTimeFormat, $f->getDateTimeFormat());
        $this->assertContains($date->format($dateTimeFormat), $f->format(array('timestamp' => $date)));
    }

    public function provideDateTimeFormats()
    {
        return array(
            array('r'),
            array('U'),
        );
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
        $date = new DateTime();
        $options = array(
            'rootElement' => 'log',
            'elementMap' => array(
                'date' => 'timestamp',
                'word' => 'message',
                'priority' => 'priority'
            ),
            'dateTimeFormat' => 'r',
        );
        $event = array(
            'timestamp' => $date,
            'message' => 'tottakai',
            'priority' => 4
        );
        $expected = sprintf('<log><date>%s</date><word>tottakai</word><priority>4</priority></log>', $date->format('r'));

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

    /**
     * @group ZF2-453
     */
    public function testFormatWillRemoveExtraEmptyArrayFromEvent()
    {
        $formatter = new XmlFormatter;
        $d = new DateTime('2001-01-01T12:00:00-06:00');
        $event = array(
            'timestamp'    =>  $d,
            'message'      => 'test',
            'priority'     => 1,
            'priorityName' => 'CRIT',
            'extra' => array()
        );
        $expected = '<logEntry><timestamp>2001-01-01T12:00:00-06:00</timestamp><message>test</message><priority>1</priority><priorityName>CRIT</priorityName></logEntry>';
        $expected .= PHP_EOL . PHP_EOL;
        $this->assertEquals($expected, $formatter->format($event));
    }
}
