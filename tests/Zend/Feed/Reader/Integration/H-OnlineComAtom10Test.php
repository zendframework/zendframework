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
namespace ZendTest\Feed\Reader\Integration;
use Zend\Feed\Reader;
use Zend\Date;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
* @group Zend_Feed
* @group Zend_Feed_Reader
*/
class HOnlineComAtom10Test extends \PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        Reader\Reader::reset();
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/h-online.com-atom10.xml';
        $this->_options = Date\Date::setOptions();
        foreach($this->_options as $k=>$v) {
            if (is_null($v)) {
                unset($this->_options[$k]);
            }
        }
        Date\Date::setOptions(array('format_type'=>'iso'));
    }
    
    public function teardown()
    {
        Date\Date::setOptions($this->_options);
    }

    public function testGetsTitle()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('The H - news feed', $feed->getTitle());
    }

    public function testGetsAuthors()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(array(array('name'=>'The H')), (array) $feed->getAuthors());
    }

    public function testGetsSingleAuthor()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(array('name'=>'The H'), $feed->getAuthor());
    }

    public function testGetsCopyright()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsDescription()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('Technology news', $feed->getDescription());
    }

    public function testGetsLanguage()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLink()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('http://www.h-online.com', $feed->getLink());
    }

    public function testGetsEncoding()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testGetsEntryCount()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(60, $feed->count());
    }

    /**
     * Entry level testing
     */

    public function testGetsEntryId()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.h-online.com/security/McAfee-update-brings-systems-down-again--/news/113689/from/rss', $entry->getId());
    }

    public function testGetsEntryTitle()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('McAfee update brings systems down again', $entry->getTitle());
    }

    public function testGetsEntryAuthors()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array(array('name'=>'The H')), (array) $entry->getAuthors());
    }

    public function testGetsEntrySingleAuthor()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array('name'=>'The H'), $entry->getAuthor());
    }

    public function testGetsEntryDescription()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        /**
         * Note: "’" is not the same as "'" - don't replace in error
         */
        $this->assertEquals('A McAfee signature update is currently causing system failures and a lot of overtime for administrators', $entry->getDescription());
    }

    public function testGetsEntryContent()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('A McAfee signature update is currently causing system failures and a lot of overtime for administrators', $entry->getContent());
    }

    public function testGetsEntryLinks()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array('http://www.h-online.com/security/McAfee-update-brings-systems-down-again--/news/113689/from/rss'), $entry->getLinks());
    }

    public function testGetsEntryLink()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.h-online.com/security/McAfee-update-brings-systems-down-again--/news/113689/from/rss', $entry->getLink());
    }

    public function testGetsEntryPermaLink()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.h-online.com/security/McAfee-update-brings-systems-down-again--/news/113689/from/rss',
            $entry->getPermaLink());
    }

    public function testGetsEntryEncoding()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

}
