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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Feed/Writer/Renderer/Feed/Atom.php';
require_once 'Zend/Feed/Reader.php';
require_once 'Zend/Version.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_Renderer_Feed_AtomTest extends PHPUnit_Framework_TestCase
{

    protected $_validWriter = null;

    public function setUp()
    {
        $this->_validWriter = new Zend_Feed_Writer_Feed;
        $this->_validWriter->setTitle('This is a test feed.');
        $this->_validWriter->setDescription('This is a test description.');
        $this->_validWriter->setDateModified(1234567890);
        $this->_validWriter->setLink('http://www.example.com');
        $this->_validWriter->setFeedLink('http://www.example.com/atom', 'atom');
        $this->_validWriter->addAuthor('Joe', 'joe@example.com', 'http://www.example.com/joe');
        
        $this->_validWriter->setType('atom');
    }

    public function tearDown()
    {
        $this->_validWriter = null;
    }

    public function testSetsWriterInConstructor()
    {
        $writer = new Zend_Feed_Writer_Feed;
        $feed = new Zend_Feed_Writer_Renderer_Feed_Atom($writer);
        $this->assertTrue($feed->getDataContainer() instanceof Zend_Feed_Writer_Feed);
    }

    public function testBuildMethodRunsMinimalWriterContainerProperlyBeforeICheckAtomCompliance()
    {
        $feed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        try {
            $feed->render();
        } catch (Zend_Feed_Exception $e) {
            $this->fail('Valid Writer object caused an exception when building which should never happen');
        }
    }

    public function testFeedEncodingHasBeenSet()
    {
        $this->_validWriter->setEncoding('iso-8859-1');
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('iso-8859-1', $feed->getEncoding());
    }

    public function testFeedEncodingDefaultIsUsedIfEncodingNotSetByHand()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testFeedTitleHasBeenSet()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('This is a test feed.', $feed->getTitle());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedTitleIfMissingThrowsException()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->remove('title');
        $atomFeed->render();
    }
    
    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedTitleCharDataEncoding()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->setTitle('<>&\'"áéíóú');
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getTitle());
    }

    public function testFeedSubtitleHasBeenSet()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('This is a test description.', $feed->getDescription());
    }
    
    public function testFeedSubtitleThrowsNoExceptionIfMissing()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->remove('description');
        $atomFeed->render();
    }
    
    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedSubtitleCharDataEncoding()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->setDescription('<>&\'"áéíóú');
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getDescription());
    }

    public function testFeedUpdatedDateHasBeenSet()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals(1234567890, $feed->getDateModified()->get(Zend_Date::TIMESTAMP));
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedUpdatedDateIfMissingThrowsException()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->remove('dateModified');
        $atomFeed->render();
    }

    public function testFeedGeneratorHasBeenSet()
    {
        $this->_validWriter->setGenerator('FooFeedBuilder', '1.00', 'http://www.example.com');
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('FooFeedBuilder', $feed->getGenerator());
    }
    
    public function testFeedGeneratorIfMissingThrowsNoException()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->remove('generator');
        $atomFeed->render();
    }

    public function testFeedGeneratorDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }
    
    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedGeneratorCharDataEncoding()
    {
        $this->_validWriter->setGenerator('<>&\'"áéíóú', '1.00', 'http://www.example.com');
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getGenerator());
    }

    public function testFeedLanguageHasBeenSet()
    {
        $this->_validWriter->setLanguage('fr');
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('fr', $feed->getLanguage());
    }
    
    public function testFeedLanguageIfMissingThrowsNoException()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->remove('language');
        $atomFeed->render();
    }

    public function testFeedLanguageDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testFeedIncludesLinkToHtmlVersionOfFeed()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }
    
    public function testFeedLinkToHtmlVersionOfFeedIfMissingThrowsNoExceptionIfIdSet()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->setId('http://www.example.com');
        $this->_validWriter->remove('link');
        $atomFeed->render();
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedLinkToHtmlVersionOfFeedIfMissingThrowsExceptionIfIdMissing()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->remove('link');
        $atomFeed->render();
    }

    public function testFeedIncludesLinkToXmlAtomWhereTheFeedWillBeAvailable()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('http://www.example.com/atom', $feed->getFeedLink());
    }
    
    /**
     * @expectedException Zend_Feed_Exception
     */
    public function testFeedLinkToXmlAtomWhereTheFeedWillBeAvailableIfMissingThrowsException()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->remove('feedLinks');
        $atomFeed->render();
    }

    public function testFeedHoldsAnyAuthorAdded()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals(array(
            'email'=>'joe@example.com',
            'name'=>'Joe',
            'uri'=>'http://www.example.com/joe'), $feed->getAuthor());
    }
    
    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedAuthorCharDataEncoding()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $this->_validWriter->remove('authors');
        $this->_validWriter->addAuthor(array(
            'email'=>'<>&\'"áéíóú',
            'name'=>'<>&\'"áéíóú',
            'uri'=>'http://www.example.com/joe'));
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals(array(
            'email'=>'<>&\'"áéíóú',
            'name'=>'<>&\'"áéíóú',
            'uri'=>'http://www.example.com/joe'), $feed->getAuthor());
    }
    
    public function testFeedAuthorIfNotSetThrowsExceptionIfAnyEntriesAlsoAreMissingAuthors()
    {
        $this->markTestIncomplete('Not yet implemented...');
    }
    
    public function testFeedAuthorIfNotSetThrowsNoExceptionIfAllEntriesIncludeAtLeastOneAuthor()
    {
        $this->markTestIncomplete('Not yet implemented...');
    }
    
    public function testFeedIdHasBeenSet()
    {
        $this->_validWriter->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $feed->getId());
    }

    public function testFeedIdDefaultOfHtmlLinkIsUsedIfNotSetByHand()
    {
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals($feed->getLink(), $feed->getId());
    }
    
    public function testBaseUrlCanBeSet()
    {
        $this->_validWriter->setBaseUrl('http://www.example.com/base');
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('http://www.example.com/base', $feed->getBaseUrl());
    }
    
    public function testCopyrightCanBeSet()
    {
        $this->_validWriter->setCopyright('Copyright © 2009 Paddy');
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('Copyright © 2009 Paddy', $feed->getCopyright());
    }
    
    public function testCopyrightCharDataEncoding()
    {
        $this->_validWriter->setCopyright('<>&\'"áéíóú');
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getCopyright());
    }
    
    public function testCategoriesCanBeSet()
    {
        $this->_validWriter->addCategories(array(
            array('term'=>'cat_dog', 'label' => 'Cats & Dogs', 'scheme' => 'http://example.com/schema1'),
            array('term'=>'cat_dog2')
        ));
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $expected = array(
            array('term'=>'cat_dog', 'label' => 'Cats & Dogs', 'scheme' => 'http://example.com/schema1'),
            array('term'=>'cat_dog2', 'label' => 'cat_dog2', 'scheme' => null)
        );
        $this->assertEquals($expected, (array) $feed->getCategories());
    }
    
    public function testCategoriesCharDataEncoding()
    {
        $this->_validWriter->addCategories(array(
            array('term'=>'cat_dog', 'label' => '<>&\'"áéíóú', 'scheme' => 'http://example.com/schema1'),
            array('term'=>'cat_dog2')
        ));
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $expected = array(
            array('term'=>'cat_dog', 'label' => '<>&\'"áéíóú', 'scheme' => 'http://example.com/schema1'),
            array('term'=>'cat_dog2', 'label' => 'cat_dog2', 'scheme' => null)
        );
        $this->assertEquals($expected, (array) $feed->getCategories());
    }
    
    public function testHubsCanBeSet()
    {
        $this->_validWriter->addHubs(
            array('http://www.example.com/hub', 'http://www.example.com/hub2')
        );
        $atomFeed = new Zend_Feed_Writer_Renderer_Feed_Atom($this->_validWriter);
        $atomFeed->render();
        $feed = Zend_Feed_Reader::importString($atomFeed->saveXml());
        $expected = array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        );
        $this->assertEquals($expected, (array) $feed->getHubs());
    }

}
