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
 * @package    Zend_GData_Books
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData;

use Zend\GData\Books;
use Zend\GData\App\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Books
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Books
 */
class BooksOnlineTest extends \PHPUnit_Framework_TestCase
{

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
            $this->assertEquals($feed->getHttpClient(), $entry->getHttpClient());
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
                $feed->getHttpClient(), $entry->getHttpClient());
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
                $feed->getHttpClient(), $entry->getHttpClient());
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
