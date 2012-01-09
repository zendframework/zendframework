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
namespace ZendTest\Feed\Reader\Entry;
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
class AtomStandaloneEntryTest extends \PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;
    
    protected $_expectedCats = array();
    
    protected $_expectedCatsDc = array();

    public function setup()
    {
        Reader\Reader::reset();
        if (\Zend\Registry::isRegistered('Zend_Locale')) {
            $registry = \Zend\Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/AtomStandaloneEntry';
        $this->_options = Date\Date::setOptions();
        foreach($this->_options as $k=>$v) {
            if (is_null($v)) {
                unset($this->_options[$k]);
            }
        }
        Date\Date::setOptions(array('format_type'=>'iso'));
        $this->_expectedCats = array(
            array(
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema1',
                'label' => 'topic1'
            ),
            array(
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema2',
                'label' => 'topic1'
            ),
            array(
                'term' => 'cat_dog',
                'scheme' => 'http://example.com/schema1',
                'label' => 'Cat & Dog'
            )
        );
        $this->_expectedCatsDc = array(
            array(
                'term' => 'topic1',
                'scheme' => null,
                'label' => 'topic1'
            ),
            array(
                'term' => 'topic2',
                'scheme' => null,
                'label' => 'topic2'
            )
        );
    }
    
    public function teardown()
    {
        Date\Date::setOptions($this->_options);
    }
    
    public function testReaderImportOfAtomEntryDocumentReturnsEntryClass()
    {
        $object = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/id/atom10.xml')
        );
        $this->assertTrue($object instanceof Reader\Entry\Atom);
    }

    /**
     * Get Id (Unencoded Text)
     * @group ZFR002
     */
    public function testGetsIdFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/id/atom10.xml')
        );
        $this->assertEquals('1', $entry->getId());
    }

    /**
     * Get creation date (Unencoded Text)
     * @group ZFR002
     */
    public function testGetsDateCreatedFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datecreated/atom10.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($entry->getDateCreated()));
    }

    /**
     * Get modification date (Unencoded Text)
     * @group ZFR002
     */
    public function testGetsDateModifiedFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datemodified/atom10.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($entry->getDateModified()));
    }

    /**
     * Get Title (Unencoded Text)
     * @group ZFR002
     */
    public function testGetsTitleFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/title/atom10.xml')
        );
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     * @group ZFR002
     */
    public function testGetsAuthorsFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/author/atom10.xml')
        );

        $authors = array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs'),
            array('email'=>'joe@example.com','uri'=>'http://www.example.com'),
            array('uri'=>'http://www.example.com'),
            array('email'=>'joe@example.com')
        );

        $this->assertEquals($authors, (array) $entry->getAuthors());
    }

    /**
     * Get Author (Unencoded Text)
     * @group ZFR002
     */
    public function testGetsAuthorFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/author/atom10.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com','uri'=>'http://www.example.com'), $entry->getAuthor());
    }

    /**
     * Get Description (Unencoded Text)
     * @group ZFR002
     */
    public function testGetsDescriptionFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/description/atom10.xml')
        );
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    /**
     * Get enclosure
     * @group ZFR002
     */
    public function testGetsEnclosureFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/enclosure/atom10.xml')
        );

        $expected = new \stdClass();
        $expected->url    = 'http://www.example.org/myaudiofile.mp3';
        $expected->length = '1234';
        $expected->type   = 'audio/mpeg';

        $this->assertEquals($expected, $entry->getEnclosure());
    }
    
    /**
     * TEXT
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/content/atom10.xml')
        );
        $this->assertEquals('Entry Content &amp;', $entry->getContent());
    }
    
    /**
     * HTML Escaped
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10Html()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/content/atom10_Html.xml')
        );
        $this->assertEquals('<p>Entry Content &amp;</p>', $entry->getContent());
    }
    
    /**
     * HTML CDATA Escaped
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10HtmlCdata()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/content/atom10_HtmlCdata.xml')
        );
        $this->assertEquals('<p>Entry Content &amp;</p>', $entry->getContent());
    }
    
    /**
     * XHTML
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10XhtmlNamespaced()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/content/atom10_Xhtml.xml')
        );
        $this->assertEquals('<p class="x:"><em>Entry Content &amp;x:</em></p>', $entry->getContent());
    }

    /**
     * Get Link (Unencoded Text)
     * @group ZFR002
     */
    public function testGetsLinkFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/link/atom10.xml')
        );
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    /**
     * Get Comment HTML Link
     * @group ZFR002
     */
    public function testGetsCommentLinkFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath . '/commentlink/atom10.xml')
        );
        $this->assertEquals('http://www.example.com/entry/comments', $entry->getCommentLink());
    }
    
    /**
     * Get category data
     * @group ZFR002
     */
    public function testGetsCategoriesFromAtom10()
    {
        $entry = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/atom10.xml')
        );
        $this->assertEquals($this->_expectedCats, (array) $entry->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($entry->getCategories()->getValues()));
    }
    
}
