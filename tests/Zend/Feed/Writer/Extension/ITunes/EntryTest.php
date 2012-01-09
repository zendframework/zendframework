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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
* @namespace
*/
namespace ZendTest\Feed\Writer\Extension\ITunes;
use Zend\Feed\Writer;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Writer
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
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
     * @expectedException Zend\Feed\Writer\Exception
     */
    public function testSetBlockThrowsExceptionOnNonAlphaValue()
    {
        $entry = new Writer\Entry;
        $entry->setItunesBlock('123');
    }
    
    /**
     * @expectedException Zend\Feed\Writer\Exception
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
     * @expectedException Zend\Feed\Writer\Exception
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
     * @expectedException Zend\Feed\Writer\Exception
     */
    public function testSetDurationThrowsExceptionOnUnknownFormat()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration('abc');
    }
    
    /**
     * @expectedException Zend\Feed\Writer\Exception
     */
    public function testSetDurationThrowsExceptionOnInvalidSeconds()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration('23:456');
    }
    
    /**
     * @expectedException Zend\Feed\Writer\Exception
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
     * @expectedException Zend\Feed\Writer\Exception
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
     * @expectedException Zend\Feed\Writer\Exception
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
     * @expectedException Zend\Feed\Writer\Exception
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
     * @expectedException Zend\Feed\Writer\Exception
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
     * @expectedException Zend\Feed\Writer\Exception
     */
    public function testSetSummaryThrowsExceptionWhenValueExceeds255Chars()
    {
        $entry = new Writer\Entry;
        $entry->setItunesSummary(str_repeat('a', 4001));
    }

}
