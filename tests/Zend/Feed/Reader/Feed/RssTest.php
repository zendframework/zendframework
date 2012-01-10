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
namespace ZendTest\Feed\Reader\Feed;
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
class RssTest extends \PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;
    
    protected $_expectedCats = array();
    
    protected $_expectedCatsRdf = array();
    
    protected $_expectedCatsAtom = array();

    public function setup()
    {
        Reader\Reader::reset();
        if (\Zend\Registry::isRegistered('Zend_Locale')) {
            $registry = \Zend\Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/Rss';
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
                'term' => 'topic2',
                'scheme' => 'http://example.com/schema1',
                'label' => 'topic2'
            )
        );
        $this->_expectedCatsRdf = array(
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
        $this->_expectedCatsAtom = array(
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
    }
    
    public function teardown()
    {
        Date\Date::setOptions($this->_options);
    }

    /**
     * Get Title (Unencoded Text)
     */
    public function testGetsTitleFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // DC 1.0

    public function testGetsTitleFromRss20_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // DC 1.1

    public function testGetsTitleFromRss20_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // Atom 1.0

    public function testGetsTitleFromRss20_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // Missing Title

    public function testGetsTitleFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     */
    public function testGetsAuthorArrayFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss20.xml')
        );
        $this->assertEquals(array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs'),
            array('email'=>'jane@example.com','name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss094.xml')
        );
        $this->assertEquals(array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs'),
            array('email'=>'jane@example.com','name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss093.xml')
        );
        $this->assertEquals(array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs'),
            array('email'=>'jane@example.com','name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss092.xml')
        );
        $this->assertEquals(array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs'),
            array('email'=>'jane@example.com','name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss091.xml')
        );
        $this->assertEquals(array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs'),
            array('email'=>'jane@example.com','name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss10.xml')
        );
        $this->assertEquals(array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs'),
            array('email'=>'jane@example.com','name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss090.xml')
        );
        $this->assertEquals(array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs'),
            array('email'=>'jane@example.com','name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    // DC 1.0

    public function testGetsAuthorArrayFromRss20_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss20.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss094.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss093.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss092.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss091.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss10.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss090.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    // DC 1.1

    public function testGetsAuthorArrayFromRss20_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss20.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss094.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss093.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss092.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss091.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss10.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss090.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    // Atom 1.0

    public function testGetsAuthorArrayFromRss20_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss20.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss094.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss093.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss092.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss091.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss10.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss090.xml')
        );
        $this->assertEquals(array(
            array('name'=>'Joe Bloggs'), array('name'=>'Jane Bloggs')
        ), (array) $feed->getAuthors());
        $this->assertEquals(array('Joe Bloggs','Jane Bloggs'), $feed->getAuthors()->getValues());
    }

    // Missing Authors

    public function testGetsAuthorArrayFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    /**
     * Get Single Author (Unencoded Text)
     */
    public function testGetsSingleAuthorFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss20.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss094.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss093.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss092.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss091.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss10.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss090.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com'), $feed->getAuthor());
    }

    // DC 1.0

    public function testGetsSingleAuthorFromRss20_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss20.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss094.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss093.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss092.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss091.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss10.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss090.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    // DC 1.1

    public function testGetsSingleAuthorFromRss20_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss20.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss094.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss093.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss092.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss091.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss10.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss090.xml')
        );
        $this->assertEquals(array('name'=>'Joe Bloggs'), $feed->getAuthor());
    }

    // Missing Author

    public function testGetsSingleAuthorFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    /**
     * Get Copyright (Unencoded Text)
     */
    public function testGetsCopyrightFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss10.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss090.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    // DC 1.0

    public function testGetsCopyrightFromRss20_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss090.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    // DC 1.1

    public function testGetsCopyrightFromRss20_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss090.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    // Missing Copyright

    public function testGetsCopyrightFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    /**
     * Get Description (Unencoded Text)
     */
    public function testGetsDescriptionFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // DC 1.0

    public function testGetsDescriptionFromRss20_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // DC 1.1

    public function testGetsDescriptionFromRss20_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // Missing Description

    public function testGetsDescriptionFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    /**
     * Get Language (Unencoded Text)
     */
    public function testGetsLanguageFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    // DC 1.0

    public function testGetsLanguageFromRss20_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss090.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    // DC 1.1

    public function testGetsLanguageFromRss20_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss090.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    // Other

    public function testGetsLanguageFromRss10_XmlLang()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rdf/rss10.xml')
        );
        $this->assertEquals('en', $feed->getLanguage());
    }

    // Missing Language

    public function testGetsLanguageFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    /**
     * Get Link (Unencoded Text)
     */
    public function testGetsLinkFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss20.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss094.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss093.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss092.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss091.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss10.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss090.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    // Missing Link

    public function testGetsLinkFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    /**
     * Implements Countable
     */

    public function testCountableInterface()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss090.xml')
        );
        $this->assertEquals(0, count($feed));
    }

    /**
     * Get Feed Link (Unencoded Text)
     */
    public function testGetsFeedLinkFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss20.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsOriginalSourceUriIfFeedLinkNotAvailableFromFeed()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss20_NoFeedLink.xml')
        );
        $feed->setOriginalSourceUri('http://www.example.com/feed/rss');
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss094.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss093.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss092.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss091.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss10.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss090.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    // Missing Feed Link

    public function testGetsFeedLinkFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    /**
     * Get Generator (Unencoded Text)
     */
    public function testGetsGeneratorFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss20.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss094.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss093.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss092.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss091.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss10.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss090.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    // Missing Generator

    public function testGetsGeneratorFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    /**
     * Get Last Build Date (Unencoded Text)
     */
    public function testGetsLastBuildDateFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/lastbuilddate/plain/rss20.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getLastBuildDate()));
    }

    public function testGetsLastBuildDateFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/lastbuilddate/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLastBuildDate());
    }

    /**
     * Get Date Modified (Unencoded Text)
     */
    public function testGetsDateModifiedFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/rss20.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    /**
     * @group ZF-8702
     */
    public function testParsesCorrectDateIfMissingOffsetWhenSystemUsesUSLocale()
    {
        $locale = new \Zend\Locale\Locale('en_US');
        \Zend\Registry::set('Zend_Locale', $locale);
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/rss20_en_US.xml')
        );
        $fdate = $feed->getDateModified();
        $edate = new Date\Date;
        $edate->set('2010-01-04T02:14:00-0600', Date\Date::ISO_8601);
        \Zend\Registry::getInstance()->offsetUnset('Zend_Locale');
        $this->assertTrue($edate->equals($fdate));
    }

    // DC 1.0

    public function testGetsDateModifiedFromRss20_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss20.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss094_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss094.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss093_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss093.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss092_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss092.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss091_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss091.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss10_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss10.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss090_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss090.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    // DC 1.1

    public function testGetsDateModifiedFromRss20_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss20.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss094_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss094.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss093_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss093.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss092_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss092.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss091_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss091.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss10_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss10.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss090_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss090.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    // Atom 1.0

    public function testGetsDateModifiedFromRss20_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss20.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss094_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss094.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss093_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss093.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss092_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss092.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss091_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss091.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss10_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss10.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss090_atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss090.xml')
        );
        $edate = new Date\Date;
        $edate->set('2009-03-07T08:03:50Z', Date\Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    // Missing DateModified

    public function testGetsDateModifiedFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    /**
     * Get Hubs (Unencoded Text)
     */
    public function testGetsHubsFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss20.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }

    public function testGetsHubsFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss094.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }

    public function testGetsHubsFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss093.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }

    public function testGetsHubsFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss092.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }

    public function testGetsHubsFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss091.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }

    public function testGetsHubsFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss10.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }

    public function testGetsHubsFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss090.xml')
        );
        $this->assertEquals(array(
            'http://www.example.com/hub1',
            'http://www.example.com/hub2'
        ), $feed->getHubs());
    }

    // Missing Hubs

    public function testGetsHubsFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }
    
    /**
     * Get category data
     */
    
    // RSS 2.0
    
    public function testGetsCategoriesFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/rss20.xml')
        );
        $this->assertEquals($this->_expectedCats, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    // DC 1.0
    
    public function testGetsCategoriesFromRss090_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss090.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss091_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss091.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss092_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss092.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss093_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss093.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss094_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss094.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss10_Dc10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss10.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    // DC 1.1
    
    public function testGetsCategoriesFromRss090_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss090.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss091_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss091.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss092_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss092.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss093_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss093.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss094_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss094.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss10_Dc11()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss10.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($feed->getCategories()->getValues()));
    }
    
    // Atom 1.0
    
    public function testGetsCategoriesFromRss090_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss090.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss091_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss091.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss092_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss092.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss093_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss093.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss094_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss094.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss10_Atom10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss10.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($feed->getCategories()->getValues()));
    }
    
    // No Categories In Entry
    
    public function testGetsCategoriesFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss20.xml')
        );
        $this->assertEquals(array(), (array) $feed->getCategories());
        $this->assertEquals(array(), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss090.xml')
        );
        $this->assertEquals(array(), (array) $feed->getCategories());
        $this->assertEquals(array(), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss091.xml')
        );
        $this->assertEquals(array(), (array) $feed->getCategories());
        $this->assertEquals(array(), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss092.xml')
        );
        $this->assertEquals(array(), (array) $feed->getCategories());
        $this->assertEquals(array(), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss093.xml')
        );
        $this->assertEquals(array(), (array) $feed->getCategories());
        $this->assertEquals(array(), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss094.xml')
        );
        $this->assertEquals(array(), (array) $feed->getCategories());
        $this->assertEquals(array(), array_values($feed->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss10.xml')
        );
        $this->assertEquals(array(), (array) $feed->getCategories());
        $this->assertEquals(array(), array_values($feed->getCategories()->getValues()));
    }

    /**
     * Get Image data (Unencoded Text)
     */
    public function testGetsImageFromRss20()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss20.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }

    public function testGetsImageFromRss094()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss094.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }

    public function testGetsImageFromRss093()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss093.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }

    public function testGetsImageFromRss092()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss092.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }

    public function testGetsImageFromRss091()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss091.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }

    /*public function testGetsImageFromRss10()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss10.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }

    public function testGetsImageFromRss090()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss090.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }*/

    /**
     * Get Image data (Unencoded Text) Missing
     */
    public function testGetsImageFromRss20_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss094_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss093_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss092_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss091_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss10_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss090_None()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

}
