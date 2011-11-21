<?php

namespace ZendTest\Mail;

use Zend\Mail\Headers,
    Zend\Mail\Header;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
    public function testHeadersImplementsProperClasses()
    {
        $headers = new Headers();
        $this->assertInstanceOf('Iterator', $headers);
        $this->assertInstanceOf('Countable', $headers);
    }

    public function testHeadersFromStringFactoryCreatesSingleObject()
    {
        $headers = Headers::fromString("Fake: foo-bar");
        $this->assertEquals(1, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());
    }

    public function testHeadersFromStringFactoryCreatesSingleObjectWithContinuationLine()
    {
        $headers = Headers::fromString("Fake: foo-bar,\r\n      blah-blah");
        $this->assertEquals(1, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar,blah-blah', $header->getFieldValue());
    }

    public function testHeadersFromStringFactoryCreatesSingleObjectWithHeaderBreakLine()
    {
        $headers = Headers::fromString("Fake: foo-bar\r\n\r\n");
        $this->assertEquals(1, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());
    }

    public function testHeadersFromStringFactoryThrowsExceptionOnMalformedHeaderLine()
    {
        $this->setExpectedException('Zend\Mail\Exception\RuntimeException', 'does not match');
        Headers::fromString("Fake = foo-bar\r\n\r\n");
    }

    public function testHeadersFromStringFactoryCreatesMultipleObjects()
    {
        $headers = Headers::fromString("Fake: foo-bar\r\nAnother-Fake: boo-baz");
        $this->assertEquals(2, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());

        $header = $headers->get('anotherfake');
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $header);
        $this->assertEquals('Another-Fake', $header->getFieldName());
        $this->assertEquals('boo-baz', $header->getFieldValue());
    }

    public function testHeadersFromStringMultiHeaderWillAggregateLazyLoadedHeaders()
    {
        $headers = new Headers();
        /* @var $pcl \Zend\Loader\PluginClassLoader */
        $pcl = $headers->getPluginClassLoader();
        $pcl->registerPlugin('foo', 'Zend\Mail\Header\GenericMultiHeader');
        $headers->addHeaderLine('foo: bar1,bar2,bar3');
        $headers->forceLoading();
        $this->assertEquals(3, $headers->count());
    }

    public function testHeadersHasAndGetWorkProperly()
    {
        $headers = new Headers();
        $headers->addHeaders(array($f = new Header\GenericHeader('Foo', 'bar'), new Header\GenericHeader('Baz', 'baz')));
        $this->assertFalse($headers->has('foobar'));
        $this->assertTrue($headers->has('foo'));
        $this->assertTrue($headers->has('Foo'));
        $this->assertSame($f, $headers->get('foo'));
    }

    public function testHeadersAggregatesHeaderObjects()
    {
        $fakeHeader = new Header\GenericHeader('Fake', 'bar');
        $headers = new Headers();
        $headers->addHeader($fakeHeader);
        $this->assertEquals(1, $headers->count());
        $this->assertSame($fakeHeader, $headers->get('Fake'));
    }

    public function testHeadersAggregatesHeaderThroughAddHeader()
    {
        $headers = new Headers();
        $headers->addHeader(new Header\GenericHeader('Fake', 'bar'));
        $this->assertEquals(1, $headers->count());
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $headers->get('Fake'));
    }

    public function testHeadersAggregatesHeaderThroughAddHeaderLine()
    {
        $headers = new Headers();
        $headers->addHeaderLine('Fake', 'bar');
        $this->assertEquals(1, $headers->count());
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $headers->get('Fake'));
    }

    public function testHeadersAddHeaderLineThrowsExceptionOnMissingFieldValue()
    {
        $this->setExpectedException('Zend\Mail\Exception\InvalidArgumentException', 'without a field');
        $headers = new Headers();
        $headers->addHeaderLine('Foo');
    }

    public function testHeadersAggregatesHeadersThroughAddHeaders()
    {
        $headers = new Headers();
        $headers->addHeaders(array(new Header\GenericHeader('Foo', 'bar'), new Header\GenericHeader('Baz', 'baz')));
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());

        $headers = new Headers();
        $headers->addHeaders(array('Foo: bar', 'Baz: baz'));
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());

        $headers = new Headers();
        $headers->addHeaders(array(array('Foo' => 'bar'), array('Baz' => 'baz')));
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());

        $headers = new Headers();
        $headers->addHeaders(array(array('Foo', 'bar'), array('Baz', 'baz')));
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());

        $headers = new Headers();
        $headers->addHeaders(array('Foo' => 'bar', 'Baz' => 'baz'));
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());
    }

    public function testHeadersAddHeadersThrowsExceptionOnInvalidArguments()
    {
        $this->setExpectedException('Zend\Mail\Exception\InvalidArgumentException', 'Expected array or Trav');
        $headers = new Headers();
        $headers->addHeaders('foo');
    }

    public function testHeadersCanRemoveHeader()
    {
        $headers = new Headers();
        $headers->addHeaders(array('Foo' => 'bar', 'Baz' => 'baz'));
        $header = $headers->get('foo');
        $this->assertEquals(2, $headers->count());
        $headers->removeHeader($header);
        $this->assertEquals(1, $headers->count());
        $this->assertFalse($headers->get('foo'));
    }

    public function testHeadersCanClearAllHeaders()
    {
        $headers = new Headers();
        $headers->addHeaders(array('Foo' => 'bar', 'Baz' => 'baz'));
        $this->assertEquals(2, $headers->count());
        $headers->clearHeaders();
        $this->assertEquals(0, $headers->count());
    }

    public function testHeadersCanBeIterated()
    {
        $headers = new Headers();
        $headers->addHeaders(array('Foo' => 'bar', 'Baz' => 'baz'));
        $iterations = 0;
        foreach ($headers as $index => $header) {
            $iterations++;
            $this->assertInstanceOf('Zend\Mail\Header\GenericHeader', $header);
            switch ($index) {
                case 0:
                    $this->assertEquals('bar', $header->getFieldValue());
                    break;
                case 1:
                    $this->assertEquals('baz', $header->getFieldValue());
                    break;
                default:
                    $this->fail('Invalid index returned from iterator');
            }
        }
        $this->assertEquals(2, $iterations);
    }

    public function testHeadersCanBeCastToString()
    {
        $headers = new Headers();
        $headers->addHeaders(array('Foo' => 'bar', 'Baz' => 'baz'));
        $this->assertEquals('Foo: bar' . "\r\n" . 'Baz: baz' . "\r\n", $headers->toString());
    }

    public function testHeadersCanBeCastToArray()
    {
        $headers = new Headers();
        $headers->addHeaders(array('Foo' => 'bar', 'Baz' => 'baz'));
        $this->assertEquals(array('Foo' => 'bar', 'Baz' => 'baz'), $headers->toArray());
    }

    public function testCastingToArrayReturnsMultiHeadersAsArrays()
    {
        $headers = new Headers();
        $received1 = Header\Received::fromString("Received: from framework (localhost [127.0.0.1])\r\nby framework (Postfix) with ESMTP id BBBBBBBBBBB\r\nfor <zend@framework>; Mon, 21 Nov 2011 12:50:27 -0600 (CST)");
        $received2 = Header\Received::fromString("Received: from framework (localhost [127.0.0.1])\r\nby framework (Postfix) with ESMTP id AAAAAAAAAAA\r\nfor <zend@framework>; Mon, 21 Nov 2011 12:50:29 -0600 (CST)");
        $headers->addHeader($received1);
        $headers->addHeader($received2);
        $array   = $headers->toArray();
        $expected = array(
            'Received' => array(
                $received1->getFieldValue(),
                $received2->getFieldValue(),
            ),
        );
        $this->assertEquals($expected, $array);
    }

    public function testCastingToStringReturnsAllMultiHeaderValues()
    {
        $headers = new Headers();
        $received1 = Header\Received::fromString("Received: from framework (localhost [127.0.0.1])\r\nby framework (Postfix) with ESMTP id BBBBBBBBBBB\r\nfor <zend@framework>; Mon, 21 Nov 2011 12:50:27 -0600 (CST)");
        $received2 = Header\Received::fromString("Received: from framework (localhost [127.0.0.1])\r\nby framework (Postfix) with ESMTP id AAAAAAAAAAA\r\nfor <zend@framework>; Mon, 21 Nov 2011 12:50:29 -0600 (CST)");
        $headers->addHeader($received1);
        $headers->addHeader($received2);
        $string  = $headers->toString();
        $expected = array(
            'Received: ' . $received1->getFieldValue(),
            'Received: ' . $received2->getFieldValue(),
        );
        $expected = implode("\r\n", $expected) . "\r\n";
        $this->assertEquals($expected, $string);
    }
}
