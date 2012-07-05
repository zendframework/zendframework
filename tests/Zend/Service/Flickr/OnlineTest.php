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
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Flickr;

use Zend\Http\Client\Adapter\Socket as SocketAdapter;
use Zend\Service\Flickr\Flickr;
use Zend\Service\Flickr\Exception\OutOfBoundsException;

/**
 * @category   Zend
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Flickr
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Flickr service consumer object
     *
     * @var Flickr
     */
    protected $flickr;

    /**
     * Socket based HTTP client adapter
     *
     * @var SocketAdapter
     */
    protected $httpClientAdapterSocket;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_FLICKR_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_Service_Flickr online tests are not enabled');
        }

        $this->flickr = new Flickr(constant('TESTS_ZEND_SERVICE_FLICKR_ONLINE_APIKEY'));

        $this->httpClientAdapterSocket = new SocketAdapter();

        $this->flickr->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->httpClientAdapterSocket);
    }

    /**
     * Basic testing to ensure that groupPoolGetPhotos works as expected
     *
     * @return void
     */
    public function testGroupPoolGetPhotosBasic()
    {
        $options = array('per_page' => 10,
                         'page'     => 1,
                         'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');

        $resultSet = $this->flickr->groupPoolGetPhotos('20083316@N00', $options);

        $this->assertGreaterThan(20000, $resultSet->totalResultsAvailable);
        $this->assertEquals(10, $resultSet->totalResults());
        $this->assertEquals(10, $resultSet->totalResultsReturned);
        $this->assertEquals(1, $resultSet->firstResultPosition);

        $this->assertEquals(0, $resultSet->key());

        try {
            $resultSet->seek(-1);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->seek(9);

        try {
            $resultSet->seek(10);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->rewind();

        $count = 0;
        foreach ($resultSet as $result) {
            $this->assertInstanceOf('Zend\Service\Flickr\Result', $result);
            $count++;
        }

        $this->assertEquals(10, $count);
    }

    /**
     * Basic testing to ensure that userSearch() works as expected
     *
     * @return void
     */
    public function testUserSearchBasic()
    {
        $options = array('per_page' => 10,
                         'page'     => 1,
                         'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');

        $resultSet = $this->flickr->userSearch('darby.felton@yahoo.com', $options);

        $this->assertEquals(16, $resultSet->totalResultsAvailable);
        $this->assertEquals(10, $resultSet->totalResults());
        $this->assertEquals(10, $resultSet->totalResultsReturned);
        $this->assertEquals(1, $resultSet->firstResultPosition);

        $this->assertEquals(0, $resultSet->key());

        try {
            $resultSet->seek(-1);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->seek(9);

        try {
            $resultSet->seek(10);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->rewind();

        $count = 0;
        foreach ($resultSet as $result) {
            $this->assertInstanceOf('Zend\Service\Flickr\Result', $result);
            $count++;
        }

        $this->assertEquals(10, $count);
    }

    /**
     * Basic testing to ensure that getIdByUsername() works as expected
     *
     * @return void
     */
    public function testGetIdByUsernameBasic()
    {
        $userId = $this->flickr->getIdByUsername('darby.felton');
        $this->assertEquals('7414329@N07', $userId);
    }

    /**
     * Ensures that tagSearch() works as expected with the sort option
     *
     * @return void
     */
    public function testTagSearchOptionSort()
    {
        $options = array(
            'per_page' => 10,
            'page'     => 1,
            'tag_mode' => 'or',
            'sort'     => 'date-taken-asc',
            'extras'   => 'license, date_upload, date_taken, owner_name, icon_server'
            );

        $resultSet = $this->flickr->tagSearch('php', $options);

        $this->assertTrue(10 < $resultSet->totalResultsAvailable);
        $this->assertEquals(10, $resultSet->totalResults());
        $this->assertEquals(10, $resultSet->totalResultsReturned);
        $this->assertEquals(1, $resultSet->firstResultPosition);

        foreach ($resultSet as $result) {
            $this->assertInstanceOf('Zend\Service\Flickr\Result', $result);
            if (isset($dateTakenPrevious)) {
                $this->assertTrue(strcmp($result->datetaken, $dateTakenPrevious) > 0);
            }
            $dateTakenPrevious = $result->datetaken;
        }
    }

    /**
     *  @group ZF-6397
     */
    public function testTotalForEmptyResultSet()
    {
        $this->assertEquals(0, $this->flickr->tagSearch('zendflickrtesttagnoresults')->totalResults());
    }
}