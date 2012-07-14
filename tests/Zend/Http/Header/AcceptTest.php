<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\GenericHeader;
use Zend\Http\Header\Accept;


class AcceptTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptFromStringCreatesValidAcceptHeader()
    {
        $acceptHeader = Accept::fromString('Accept: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $acceptHeader);
        $this->assertInstanceOf('Zend\Http\Header\Accept', $acceptHeader);
    }

    public function testAcceptGetFieldNameReturnsHeaderName()
    {
        $acceptHeader = new Accept();
        $this->assertEquals('Accept', $acceptHeader->getFieldName());
    }

    public function testAcceptGetFieldValueReturnsProperValue()
    {
        $acceptHeader = Accept::fromString('Accept: xxx');
        $this->assertEquals('xxx', $acceptHeader->getFieldValue());
    }

    public function testAcceptToStringReturnsHeaderFormattedString()
    {
        $acceptHeader = new Accept();
        $acceptHeader->addMediaType('text/html', 0.8)
                     ->addMediaType('application/json', 1)
                     ->addMediaType('application/atom+xml', 0.9);

        // @todo set some values, then test output
        $this->assertEquals(
            'Accept: text/html;q=0.8, application/json, application/atom+xml;q=0.9',
            $acceptHeader->toString());
    }


    /** Implementation specific tests here */

    public function testCanParseCommaSeparatedValues()
    {
        $header = Accept::fromString('Accept: text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c');
        $this->assertTrue($header->hasMediaType('text/plain'));
        $this->assertTrue($header->hasMediaType('text/html'));
        $this->assertTrue($header->hasMediaType('text/x-dvi'));
        $this->assertTrue($header->hasMediaType('text/x-c'));
    }

    public function testPrioritizesValuesBasedOnQParameter()
    {
        $header   = Accept::fromString('Accept: text/plain; q=0.5, text/html, text/xml; q=0, text/x-dvi; q=0.8, text/x-c');
        $expected = array(
            'text/html',
            'text/x-c',
            'text/x-dvi',
            'text/plain',
            'text/xml',
        );

        $test = array();
        foreach($header->getPrioritized() as $type) {
            $this->assertEquals(array_shift($expected), $type->typeString);
        }
    }

    public function testLevel()
    {
        $acceptHeader = new Accept();
        $acceptHeader->addMediaType('text/html', 0.8, array('level' => 1))
                     ->addMediaType('text/html', 0.4, array('level' => 2))
                     ->addMediaType('application/atom+xml', 0.9);

        $this->assertEquals(
                        'Accept: text/html;q=0.8;level=1, '
                           .'text/html;q=0.4;level=2, application/atom+xml;q=0.9',
                        $acceptHeader->toString()
                );
    }

    public function testPrioritizedLevel()
    {
        $header = Accept::fromString('Accept: text/*;q=0.3, text/html;q=0.7, text/html;level=1,'
                                     .'text/html;level=2;q=0.4, */*;q=0.5');

        $expected = array (
            'text/html;level=1',
            'text/html;q=0.7',
            '*/*;q=0.5',
            'text/html;level=2;q=0.4',
            'text/*;q=0.3'
        );

        $test = array();
        foreach($header->getPrioritized() as $type) {
            $this->assertEquals(array_shift($expected), $type->raw);
        }
    }

    public function testWildcharMediaType()
    {
        $acceptHeader = new Accept();
        $acceptHeader->addMediaType('text/*', 0.8)
                     ->addMediaType('application/*', 1)
                     ->addMediaType('*/*', 0.4);

        $this->assertTrue($acceptHeader->hasMediaType('text/html'));
        $this->assertTrue($acceptHeader->hasMediaType('application/atom+xml'));
        $this->assertTrue($acceptHeader->hasMediaType('audio/basic'));
        $this->assertEquals('Accept: application/*, text/*;q=0.8, */*;q=0.4', $acceptHeader->toString());
    }


    public function testMatchWildCard()
    {
        $acceptHeader = Accept::fromString('Accept: */*');
        $this->assertTrue($acceptHeader->hasMediaType('application/vnd.foobar+json'));

        $acceptHeader = Accept::fromString('Accept: application/*');
        $this->assertTrue($acceptHeader->hasMediaType('application/vnd.foobar+json'));
        $this->assertTrue($acceptHeader->hasMediaType('application/vnd.foobar+*'));

        $acceptHeader = Accept::fromString('Accept: application/vnd.foobar+html');
        $this->assertTrue($acceptHeader->hasMediaType('*/html'));
        $this->assertTrue($acceptHeader->hasMediaType('application/vnd.foobar+*'));

        $acceptHeader = Accept::fromString('Accept: text/html');
        $this->assertTrue($acceptHeader->hasMediaType('*/html'));
        $this->assertTrue($acceptHeader->hasMediaType('*/*+html'));

        $this->assertTrue($acceptHeader->hasMediaType('text/*'));
    }


    public function testParsingAndAssemblingQuotedStrings()
    {
        $acceptStr = 'Accept: application/vnd.foobar+html;q=1;version="2\\'
                   . chr(22).'3\"";level="foo;, bar", text/json;level=1, text/xml;level=2;q=0.4';
        $acceptHeader = Accept::fromString($acceptStr);

        $this->assertEquals($acceptStr, $acceptHeader->getFieldName().': '.$acceptHeader->getFieldValue());
    }


    public function testVersioning()
    {
        $acceptStr = 'Accept: text/html;q=1; version=23; level=5, text/json;level=1,' .
                'text/xml;level=2;q=0.4';
        $acceptHeader = Accept::fromString($acceptStr);

        $expected = array('typeString' => 'text/html',
                'type' => 'text',
                'subtype' => 'html',
                'subtypeRaw' => 'html',
                'format' => 'html',
                'priority' => 1,
                'params' => array('q' => 1, 'version' => 23, 'level' => 5),
                'raw' => 'text/html;q=1; version=23; level=5');

        $this->assertFalse($acceptHeader->match('text/html; version=22'));

        $res = $acceptHeader->match('text/html; version=23');
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $res->$key);
        }

        $this->assertFalse($acceptHeader->match('text/html; version=24'));

        $res = $acceptHeader->match('text/html; version=22-24');
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $res->$key);
        }

        $this->assertFalse($acceptHeader->match('text/html; version=20|22|24'));

        $res = $acceptHeader->match('text/html; version=22|23|24');
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $res->$key);
        }

    }

    public function testVersioningAndPriorization()
    {
        $acceptStr = 'Accept: text/html; version=23, text/json; version=15.3; q=0.9,' .
                'text/html;level=2;q=0.4';
        $acceptHeader = Accept::fromString($acceptStr);

        $expected = array('typeString' => 'text/json',
                'type' => 'text',
                'subtype' => 'json',
                'subtypeRaw' => 'json',
                'format' => 'json',
                'priority' => 0.9,
                'params' => array('q' => 0.9, 'version' => 15.3),
                'raw' => 'text/json; version=15.3; q=0.9');

        $str = 'text/html; version=17, text/json; version=15-16';
        $res = $acceptHeader->match($str);
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $res->$key);
        }

        $expected = (object) array('typeString' => 'text/html',
                'type' => 'text',
                'subtype' => 'html',
                'subtypeRaw' => 'html',
                'format' => 'html',
                'priority' => 0.4,
                'params' => array('q' => 0.4, 'level' => 2),
                'raw' => 'text/html;level=2;q=0.4');

        $str = 'text/html; version=17,text/json; version=15-16; q=0.5';
        $res = $acceptHeader->match($str);
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $res->$key);
        }
    }


    public function testPrioritizing()
    {
        // Example is copy/paste from rfc2616
        $acceptStr = 'Accept: text/*;q=0.3, */*,text/html;q=1, text/html;level=1,'
                           . 'text/html;level=2;q=0.4, */*;q=0.5';
        $acceptHdr = Accept::fromString($acceptStr);

        $expected = array('typeString' => 'text/html',
                'type' => 'text',
                'subtype' => 'html',
                'subtypeRaw' => 'html',
                'format' => 'html',
                'priority' => 1,
                'params' => array('level' => 1),
                'raw' => 'text/html;level=1');

        $res = $acceptHdr->match('text/html');
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $res->$key);
        }

        $res = $acceptHdr->match('text');
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $res->$key);
        }

    }

    public function testWildcardDefaults()
    {
        $this->markTestIncomplete('No wildcard defaults implemented yet');

        $expected = (object)array('typeString' => 'image',
                'type' => 'image',
                'subtype' => '*',
                'subtypeRaw' => '',
                'format' => 'jpeg',
                'priority' => 1,
                'params' => array(),
                'raw' => 'image');

        $this->assertEquals($expected, $acceptHdr->match('image'));
        //  $this->assertEquals($expected, $this->_handler->match('text'));
    }

}
