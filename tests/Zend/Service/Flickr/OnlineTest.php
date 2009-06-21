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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';


/**
 * @category   Zend
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Flickr_OnlineTest extends PHPUnit_Framework_TestCase
{
    /**
     * Reference to Flickr service consumer object
     *
     * @var Zend_Service_Flickr
     */
    protected $_flickr;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Socket
     */
    protected $_httpClientAdapterSocket;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        /**
         * @see Zend_Service_Flickr
         */
        require_once 'Zend/Service/Flickr.php';
        $this->_flickr = new Zend_Service_Flickr(constant('TESTS_ZEND_SERVICE_FLICKR_ONLINE_APIKEY'));

        /**
         * @see Zend_Http_Client_Adapter_Socket
         */
        require_once 'Zend/Http/Client/Adapter/Socket.php';
        $this->_httpClientAdapterSocket = new Zend_Http_Client_Adapter_Socket();

        $this->_flickr->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterSocket);
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
                         
        $resultSet = $this->_flickr->groupPoolGetPhotos('20083316@N00', $options);
        
        $this->assertEquals(21770, $resultSet->totalResultsAvailable);
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
            $this->assertTrue($result instanceof Zend_Service_Flickr_Result);
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

        $resultSet = $this->_flickr->userSearch('darby.felton@yahoo.com', $options);

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
            $this->assertTrue($result instanceof Zend_Service_Flickr_Result);
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
        $userId = $this->_flickr->getIdByUsername('darby.felton');
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

        $resultSet = $this->_flickr->tagSearch('php', $options);

        $this->assertTrue(10 < $resultSet->totalResultsAvailable);
        $this->assertEquals(10, $resultSet->totalResults());
        $this->assertEquals(10, $resultSet->totalResultsReturned);
        $this->assertEquals(1, $resultSet->firstResultPosition);

        foreach ($resultSet as $result) {
            $this->assertTrue($result instanceof Zend_Service_Flickr_Result);
            if (isset($dateTakenPrevious)) {
                $this->assertTrue(strcmp($result->datetaken, $dateTakenPrevious) > 0);
            }
            $dateTakenPrevious = $result->datetaken;
        }
    }
}


class Zend_Service_Flickr_OnlineTest_Skip extends PHPUnit_Framework_TestCase
{
    public function testNothing()
    {
        $this->markTestSkipped('Zend_Service_Flickr online tests not enabled in TestConfiguration.php');
    }
}