<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Writer\Extension\ITunes;

use Zend\Feed\Writer;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Writer
*/
class EntryTest extends \PHPUnit_Framework_TestCase
{

    public function testSetBlock()
    {
        $entry = new Writer\Entry;
        $entry->setItunesBlock('yes');
        $this->assertEquals('yes', $entry->getItunesBlock());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetBlockThrowsExceptionOnNonAlphaValue()
    {
        $entry = new Writer\Entry;
        $entry->setItunesBlock('123');
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetBlockThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $entry = new Writer\Entry;
        $entry->setItunesBlock(str_repeat('a', 256));
    }

    public function testAddAuthors()
    {
        $entry = new Writer\Entry;
        $entry->addItunesAuthors(array('joe', 'jane'));
        $this->assertEquals(array('joe', 'jane'), $entry->getItunesAuthors());
    }

    public function testAddAuthor()
    {
        $entry = new Writer\Entry;
        $entry->addItunesAuthor('joe');
        $this->assertEquals(array('joe'), $entry->getItunesAuthors());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testAddAuthorThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $entry = new Writer\Entry;
        $entry->addItunesAuthor(str_repeat('a', 256));
    }

    public function testSetDurationAsSeconds()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration(23);
        $this->assertEquals(23, $entry->getItunesDuration());
    }

    public function testSetDurationAsMinutesAndSeconds()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration('23:23');
        $this->assertEquals('23:23', $entry->getItunesDuration());
    }

    public function testSetDurationAsHoursMinutesAndSeconds()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration('23:23:23');
        $this->assertEquals('23:23:23', $entry->getItunesDuration());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetDurationThrowsExceptionOnUnknownFormat()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration('abc');
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetDurationThrowsExceptionOnInvalidSeconds()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration('23:456');
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetDurationThrowsExceptionOnInvalidMinutes()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration('23:234:45');
    }

    public function testSetExplicitToYes()
    {
        $entry = new Writer\Entry;
        $entry->setItunesExplicit('yes');
        $this->assertEquals('yes', $entry->getItunesExplicit());
    }

    public function testSetExplicitToNo()
    {
        $entry = new Writer\Entry;
        $entry->setItunesExplicit('no');
        $this->assertEquals('no', $entry->getItunesExplicit());
    }

    public function testSetExplicitToClean()
    {
        $entry = new Writer\Entry;
        $entry->setItunesExplicit('clean');
        $this->assertEquals('clean', $entry->getItunesExplicit());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetExplicitThrowsExceptionOnUnknownTerm()
    {
        $entry = new Writer\Entry;
        $entry->setItunesExplicit('abc');
    }

    public function testSetKeywords()
    {
        $entry = new Writer\Entry;
        $words = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12'
        );
        $entry->setItunesKeywords($words);
        $this->assertEquals($words, $entry->getItunesKeywords());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetKeywordsThrowsExceptionIfMaxKeywordsExceeded()
    {
        $entry = new Writer\Entry;
        $words = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13'
        );
        $entry->setItunesKeywords($words);
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetKeywordsThrowsExceptionIfFormattedKeywordsExceeds255CharLength()
    {
        $entry = new Writer\Entry;
        $words = array(
            str_repeat('a', 253), str_repeat('b', 2)
        );
        $entry->setItunesKeywords($words);
    }

    public function testSetSubtitle()
    {
        $entry = new Writer\Entry;
        $entry->setItunesSubtitle('abc');
        $this->assertEquals('abc', $entry->getItunesSubtitle());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetSubtitleThrowsExceptionWhenValueExceeds255Chars()
    {
        $entry = new Writer\Entry;
        $entry->setItunesSubtitle(str_repeat('a', 256));
    }

    public function testSetSummary()
    {
        $entry = new Writer\Entry;
        $entry->setItunesSummary('abc');
        $this->assertEquals('abc', $entry->getItunesSummary());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testSetSummaryThrowsExceptionWhenValueExceeds255Chars()
    {
        $entry = new Writer\Entry;
        $entry->setItunesSummary(str_repeat('a', 4001));
    }

}
