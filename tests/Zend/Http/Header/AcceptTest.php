<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\GenericHeader,
    Zend\Http\Header\Accept;


class foobar2 {
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
        $this->assertEquals('Accept: text/html;q=0.8,application/json,application/atom+xml;q=0.9', $acceptHeader->toString());
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
        );

        $test = array();
        foreach($header->getPrioritized() as $type) {
            $test[] = $type;
        }
        $this->assertEquals($expected, $test);
    }

    public function testLevel()
    {
        $acceptHeader = new Accept();
        $acceptHeader->addMediaType('text/html', 0.8, 1)
                     ->addMediaType('text/html', 0.4, 2)
                     ->addMediaType('application/atom+xml', 0.9);

        $this->assertEquals('Accept: text/html;level=1;q=0.8,text/html;level=2;q=0.4,application/atom+xml;q=0.9', $acceptHeader->toString());
    }

    public function testPrioritizedLevel()
    {
        $header = Accept::fromString('Accept: text/*;q=0.3, text/html;q=0.7, text/html;level=1,text/html;level=2;q=0.4, */*;q=0.5');

        $expected = array (
            'text/html;level=1',
            'text/html',
            '*/*',
            'text/html;level=2',
            'text/*'
        );

        $test = array();
        foreach($header->getPrioritized() as $type) {
            $test[] = $type;
        }
        $this->assertEquals($expected, $test);
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
        $this->assertEquals('Accept: text/*;q=0.8,application/*,*/*;q=0.4', $acceptHeader->toString());
    }


/*****************************************************************************************/
    /*****************************************************************************************/
    /*****************************************************************************************/
    /*****************************************************************************************/

    public function testInvalidHeader()
    {
        try {
            $acceptHeader = Accept::fromString('*/*'); // 'Accept: ' should be prepended
            $this->fail('exception expected');
        } catch (\Zend\Http\Header\Exception\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'Invalid header line for Accept header string');
        }
    }


    public function testMatchWildCard()
    {
        $acceptHeader = Accept::fromString('Accept: */*');
        $this->assertTrue($acceptHeader->hasMediaType('application/vnd.foobar+json'));

        $acceptHeader = Accept::fromString('Accept: application/*');
        $this->assertTrue($acceptHeader->hasMediaType('application/vnd.foobar+json'));
        $this->assertTrue($acceptHeader->hasMediaType('application/vnd.foobar+*'));
    }
 }

    class AcceptTest extends \PHPUnit_Framework_TestCase
    {

    public function testParsingAndAssemblingQuotedStrings()
    {
        ini_set('display_errors', 1);
        error_reporting(-1);
        $acceptStr = 'Accept: application/vnd.foobar+html;q=1;version="2\\'
                   . chr(22).'3\"";level="foo;, bar", text/json;level=1, text/xml;level=2;q=0.4';
        $acceptHeader = Accept::fromString($acceptStr);

        $this->assertEquals($acceptStr, 'Accept: '.$acceptHeader->getFieldValue());
// var_dump($acceptHeader);
//         var_dump($acceptHeader->getPrioritized());
    }

}
class foobar {
//     function foboar() {


    public function testVersioning()
    {
        $request = new RequestMock('http://fobar/jhkjh', $this->_bootstrap);
        $request->setHeader('Accept', 'text/html;q=1; version=23; level=5, text/json;level=1,' .
                'text/xml;level=2;q=0.4');

        $this->_handler->setRequest($request);
        $this->_handler->setAllowedFormats(array('json', 'html', 'image' => 'jpeg'));

        $expected = array('typeString' => 'text/html',
                'type' => 'text',
                'subtype' => 'html',
                'subtypeRaw' => 'html',
                'format' => 'html',
                'priority' => 1,
                'params' => array('q' => 1, 'version' => 23, 'level' => 5),
                'raw' => 'text/html;q=1; version=23; level=5');

        $this->assertFalse($this->_handler->match('text/html; version=22', false, false));
        $this->assertEquals($expected, $this->_handler->match('text/html; version=23', false, false));
        $this->assertFalse($this->_handler->match('text/html; version=24', false, false));

        $this->assertEquals($expected, $this->_handler->match('text/html; version=22-24', false, false));
        $this->assertFalse($this->_handler->match('text/html; version=20|22|24', false, false));
        $this->assertEquals($expected, $this->_handler->match('text/html; version=22|23|24', false, false));
    }

    public function testMoreVersioning()
    {
        $request = new RequestMock('http://fobar/jhkjh', $this->_bootstrap);
        $request->setHeader('Accept', 'text/html; version=23, text/json; version=15.3; q=0.9,' .
                'text/html;level=2;q=0.4');

        $this->_handler->setRequest($request);
        $this->_handler->setAllowedFormats(array('json', 'html'));

        $expected = array('typeString' => 'text/json',
                'type' => 'text',
                'subtype' => 'json',
                'subtypeRaw' => 'json',
                'format' => 'json',
                'priority' => 0.9,
                'params' => array('q' => 0.9, 'version' => 15.3),
                'raw' => 'text/json; version=15.3; q=0.9');
        $this->assertEquals($expected, $this->_handler->match(array('text/html; version=17', 'text/json; version=15-16'), false, false));

        $expected = array('typeString' => 'text/html',
                'type' => 'text',
                'subtype' => 'html',
                'subtypeRaw' => 'html',
                'format' => 'html',
                'priority' => 0.4,
                'params' => array('q' => 0.4, 'level' => 2),
                'raw' => 'text/html;level=2;q=0.4');
        $this->assertEquals($expected, $this->_handler->match(array('text/html; version=17', 'text/json; version=15-16; q=0.5'), false, false));
    }

    public function testIsPluginEnabled()
    {
        $front = Zend_Controller_Front::getInstance();

        $pluginName = 'Idm_Controller_Plugin_AcceptHandler';
        $plugins = array();
        foreach($front->getPlugins() as $class) {
            $plugins[] = get_class($class);
        }
        $this->assertContains($pluginName, $plugins, 'Plugin "' . $pluginName . '" should be loaded');
    }























    public function testPrioritizing()
    {
        $request = new RequestMock('http://fobar/jhkjh', $this->_bootstrap);
        $request->setHeader('Accept', 'text/*;q=0.3, */*,text/html;q=1, text/html;level=1,' .
                'text/html;level=2;q=0.4, */*;q=0.5');

        $this->_handler->setRequest($request);
        $this->_handler->setAllowedFormats(array('json', 'html', 'image' => 'jpeg'));

        $expected = array('typeString' => 'text/html',
                'type' => 'text',
                'subtype' => 'html',
                'subtypeRaw' => 'html',
                'format' => 'html',
                'priority' => 1,
                'params' => array('level' => 1),
                'raw' => 'text/html;level=1');

        $this->assertEquals($expected, $this->_handler->match('text/html', false, false));
        $this->assertEquals($expected, $this->_handler->match('text', false, false));

        $expected = array('typeString' => 'image',
                'type' => 'image',
                'subtype' => '*',
                'subtypeRaw' => '',
                'format' => 'jpeg',
                'priority' => 1,
                'params' => array(),
                'raw' => 'image');

        $this->assertEquals($expected, $this->_handler->match('image', false, false));
        //            $this->assertEquals($expected, $this->_handler->match('text'));
    }
}
