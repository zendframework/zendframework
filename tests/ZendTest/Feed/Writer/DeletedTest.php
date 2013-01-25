<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Writer;

use DateTime;
use Zend\Feed\Writer;

/**
* @category Zend
* @package Zend_Exception
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Writer
*/
class DeletedTest extends \PHPUnit_Framework_TestCase
{

    public function testSetsReference()
    {
        $entry = new Writer\Deleted;
        $entry->setReference('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $entry->getReference());
    }

    public function testSetReferenceThrowsExceptionOnInvalidParameter()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setReference('');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetReferenceReturnsNullIfNotSet()
    {
        $entry = new Writer\Deleted;
        $this->assertTrue(is_null($entry->getReference()));
    }

    public function testSetWhenDefaultsToCurrentTime()
    {
        $entry = new Writer\Deleted;
        $entry->setWhen();
        $dateNow = new DateTime();
        $this->assertTrue($dateNow >= $entry->getWhen());
    }

    public function testSetWhenUsesGivenUnixTimestamp()
    {
        $entry = new Writer\Deleted;
        $entry->setWhen(1234567890);
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $entry->getWhen());
    }

    /**
     * @group ZF-12070
     */
    public function testSetWhenUsesGivenUnixTimestampWhenItIsLessThanTenDigits()
    {
        $entry = new Writer\Deleted;
        $entry->setWhen(123456789);
        $myDate = new DateTime('@' . 123456789);
        $this->assertEquals($myDate, $entry->getWhen());
    }

    /**
     * @group ZF-11610
     */
    public function testSetWhenUsesGivenUnixTimestampWhenItIsAVerySmallInteger()
    {
        $entry = new Writer\Deleted;
        $entry->setWhen(123);
        $myDate = new DateTime('@' . 123);
        $this->assertEquals($myDate, $entry->getWhen());
    }

    public function testSetWhenUsesDateTimeObject()
    {
        $myDate = new DateTime('@' . 1234567890);
        $entry = new Writer\Deleted;
        $entry->setWhen($myDate);
        $this->assertEquals($myDate, $entry->getWhen());
    }

    public function testSetWhenThrowsExceptionOnInvalidParameter()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setWhen('abc');
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testGetWhenReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Deleted;
        $this->assertTrue(is_null($entry->getWhen()));
    }

    public function testAddsByNameFromArray()
    {
        $entry = new Writer\Deleted;
        $entry->setBy(array('name'=>'Joe'));
        $this->assertEquals(array('name'=>'Joe'), $entry->getBy());
    }

    public function testAddsByEmailFromArray()
    {
        $entry = new Writer\Deleted;
        $entry->setBy(array('name'=>'Joe','email'=>'joe@example.com'));
        $this->assertEquals(array('name'=>'Joe', 'email' => 'joe@example.com'), $entry->getBy());
    }

    public function testAddsByUriFromArray()
    {
        $entry = new Writer\Deleted;
        $entry->setBy(array('name'=>'Joe','uri'=>'http://www.example.com'));
        $this->assertEquals(array('name'=>'Joe', 'uri' => 'http://www.example.com'), $entry->getBy());
    }

    public function testAddByThrowsExceptionOnInvalidNameFromArray()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setBy(array('name'=>''));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testAddByThrowsExceptionOnInvalidEmailFromArray()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setBy(array('name'=>'Joe','email'=>''));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testAddByThrowsExceptionOnInvalidUriFromArray()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $entry = new Writer\Deleted;
        try {
            $entry->setBy(array('name'=>'Joe','uri'=>'notauri'));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    public function testAddByThrowsExceptionIfNameOmittedFromArray()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setBy(array('uri'=>'notauri'));
            $this->fail();
        } catch (Writer\Exception\ExceptionInterface $e) {
        }
    }

    /**
     * @covers Zend\Feed\Writer\Deleted::getBy
     */
    public function testGetBy()
    {
        $entry = new Writer\Deleted;

        $by = $entry->getBy();
        $this->assertNull($by);

        $entry->setBy(array('name'=>'Joe','email'=>'joe@example.com'));
        $this->assertEquals(array('name'=>'Joe', 'email' => 'joe@example.com'), $entry->getBy());
    }


    public function testSetByException()
    {
        $entry = new Writer\Deleted;

        $this->setExpectedException('Zend\Feed\Writer\Exception\InvalidArgumentException',
            'Invalid parameter: "uri" array value must be a non-empty string and valid URI/IRI');
        $entry->setBy(array('name' => 'joe','email'=>'joe@example.com', 'uri'=> ''));
    }

    /**
     * @covers Zend\Feed\Writer\Deleted::getComment
     * @covers Zend\Feed\Writer\Deleted::setComment
     * @covers Zend\Feed\Writer\Deleted::remove
     */
    public function testCommentAndRemove()
    {
        $entry = new Writer\Deleted;

        $comment = $entry->getComment();
        $this->assertNull($comment);

        $entry->setComment('foo');
        $this->assertEquals('foo', $entry->getComment());

        $entry->remove('comment');
        $this->assertNull($entry->getComment());
    }

    /**
     * @covers Zend\Feed\Writer\Deleted::getEncoding
     * @covers Zend\Feed\Writer\Deleted::setEncoding
     */
    public function testEncoding()
    {
        $entry = new Writer\Deleted;

        $encoding = $entry->getEncoding();
        $this->assertEquals('UTF-8', $encoding);

        $entry->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $entry->getEncoding());

        $this->setExpectedException('Zend\Feed\Writer\Exception\InvalidArgumentException',
            'Invalid parameter: parameter must be a non-empty string');
        $entry->setEncoding(null);
    }

    /**
     * @covers Zend\Feed\Writer\Deleted::getType
     * @covers Zend\Feed\Writer\Deleted::setType
     */
    public function testType()
    {
        $entry = new Writer\Deleted;

        $type = $entry->getType();
        $this->assertNull($type);

        $entry->setType('atom');
        $this->assertEquals('atom', $entry->getType());
    }

    public function testFluentInterface()
    {
        $entry = new Writer\Deleted;

        $result = $entry->setType('type')
                        ->setBy(array('name' => 'foo'))
                        ->setComment('comment')
                        ->setEncoding('utf-8')
                        ->setReference('foo')
                        ->setWhen(null);

        $this->assertSame($result, $entry);
    }
}
