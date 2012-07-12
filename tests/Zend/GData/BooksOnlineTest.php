<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData;

use Zend\GData\Books;
use Zend\GData\App\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Books
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Books
 */
class BooksOnlineTest extends \PHPUnit_Framework_TestCase
{

    /** @var Books */
    public $gdata;

    public function setUp()
    {
        if (!constant('TESTS_ZEND_GDATA_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_GData online tests are not enabled');
        }
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $service = Books::AUTH_SERVICE_NAME;
        $client = \Zend\GData\ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Books($client);
    }

    public function testGetVolumeFeed()
    {
        $query = $this->gdata->newVolumeQuery();
        $query->setQuery('Hamlet');
        $query->setStartIndex(5);
        $query->setMaxResults(8);
        $query->setMinViewability('partial_view');
        $feed = $this->gdata->getVolumeFeed($query);

        $this->assertTrue($feed instanceof Books\VolumeFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Books\VolumeEntry);
            $this->assertEquals($feed->getService(), $entry->getService());
        }

        $this->assertEquals(5, $feed->startIndex->text);
        $this->assertEquals(8, $feed->itemsPerPage->text);
    }

    public function testGetVolumetEntry()
    {
        $entry = $this->gdata->getVolumeEntry('Mfer_MFwQrkC');
        $this->assertTrue($entry instanceof Books\VolumeEntry);
    }

    public function testUserLibraryFeed()
    {
        $feed = $this->gdata->getUserLibraryFeed();
        $this->assertTrue($feed instanceof Books\VolumeFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Books\VolumeEntry);
            $this->assertEquals(
                $feed->getService(), $entry->getService());
        }

        $entry = new Books\VolumeEntry();
        $entry->setId(new Extension\Id('Mfer_MFwQrkC'));
        $newEntry = $this->gdata->insertVolume($entry);
        $this->assertTrue($newEntry instanceof Books\VolumeEntry);
        $this->gdata->deleteVolume($newEntry);
    }

    public function testUserAnnotationFeed()
    {
        $feed = $this->gdata->getUserAnnotationFeed();
        $this->assertTrue($feed instanceof Books\VolumeFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Books\VolumeEntry);
            $this->assertEquals(
                $feed->getService(), $entry->getService());
        }

        $entry = new Books\VolumeEntry();
        $entry->setId(new Extension\Id('Mfer_MFwQrkC'));
        $entry->setRating(new \Zend\GData\Extension\Rating(3, 1, 5, 1));
        $newEntry = $this->gdata->insertVolume($entry,
            Books::MY_ANNOTATION_FEED_URI);
        $this->assertTrue($newEntry instanceof Books\VolumeEntry);
        $this->gdata->deleteVolume($newEntry);
    }
}
