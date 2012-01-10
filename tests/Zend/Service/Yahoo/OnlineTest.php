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
 * @package    Zend_Service_Yahoo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_Service_Yahoo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Yahoo
 */
class Zend_Service_Yahoo_OnlineTest extends PHPUnit_Framework_TestCase
{
    /**
     * Reference to Yahoo service consumer object
     *
     * @var Zend_Service_Yahoo
     */
    protected $_yahoo;

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
        if (!constant('TESTS_ZEND_SERVICE_YAHOO_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_Service_Yahoo online tests are not enabled');
        }

        $this->_yahoo = new Zend_Service_Yahoo(constant('TESTS_ZEND_SERVICE_YAHOO_ONLINE_APPID'));

        $this->_httpClientAdapterSocket = new Zend_Http_Client_Adapter_Socket();

        $this->_yahoo->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterSocket);
    }

    /**
     * Ensures that inlinkDataSearch() works as expected given 'http://framework.zend.com/' as a query
     *
     * @return void
     */
    public function testInlinkDataSearchPhp()
    {
        $inlinkDataResultSet = $this->_yahoo->inlinkDataSearch('http://framework.zend.com/');

        $this->assertTrue($inlinkDataResultSet instanceof Zend_Service_Yahoo_InlinkDataResultSet);
        $this->assertTrue($inlinkDataResultSet->totalResultsAvailable > 10);
        $this->assertEquals(50, $inlinkDataResultSet->totalResultsReturned);
        $this->assertEquals(50, $inlinkDataResultSet->totalResults());
        $this->assertEquals(1, $inlinkDataResultSet->firstResultPosition);
        $this->assertEquals(0, $inlinkDataResultSet->key());

        try {
            $inlinkDataResultSet->seek(-1);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        foreach ($inlinkDataResultSet as $inlinkDataResult) {
            $this->assertTrue($inlinkDataResult instanceof Zend_Service_Yahoo_InlinkDataResult);
        }

        $this->assertEquals(50, $inlinkDataResultSet->key());
        $inlinkDataResultSet->seek(0);
        $this->assertEquals(0, $inlinkDataResultSet->key());
    }

    /**
     * Ensures that imageSearch() works as expected given 'php' as a query
     *
     * @return void
     */
    public function testImageSearchPhp()
    {
        $imageResultSet = $this->_yahoo->imageSearch('php');

        $this->assertTrue($imageResultSet instanceof Zend_Service_Yahoo_ImageResultSet);
        $this->assertTrue($imageResultSet->totalResultsAvailable > 10);
        $this->assertEquals(10, $imageResultSet->totalResultsReturned);
        $this->assertEquals(10, $imageResultSet->totalResults());
        $this->assertEquals(1, $imageResultSet->firstResultPosition);
        $this->assertEquals(0, $imageResultSet->key());

        try {
            $imageResultSet->seek(-1);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        foreach ($imageResultSet as $imageResult) {
            $this->assertTrue($imageResult instanceof Zend_Service_Yahoo_ImageResult);
        }

        $this->assertEquals(10, $imageResultSet->key());
        $imageResultSet->seek(0);
        $this->assertEquals(0, $imageResultSet->key());
    }

    /**
     * Ensures that imageSearch() throws an exception when the adult_ok option is invalid
     *
     * @return void
     */
    public function testImageSearchExceptionAdultOkInvalid()
    {
        try {
            $this->_yahoo->imageSearch('php', array('adult_ok' => -1));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('error occurred sending request', $e->getMessage());
        }
    }

    /**
     * Ensures that localSearch() works as expected when searching for restaurants in ZIP 95014
     *
     * @return void
     */
    public function testLocalSearchRestaurants()
    {
        $localResultSet = $this->_yahoo->localSearch('restaurants', array('zip' => '95014'));

        $this->assertTrue($localResultSet instanceof Zend_Service_Yahoo_LocalResultSet);

        $this->assertTrue($localResultSet->totalResultsAvailable > 10);
        $this->assertEquals(10, $localResultSet->totalResultsReturned);
        $this->assertEquals(10, $localResultSet->totalResults());
        $this->assertEquals(1, $localResultSet->firstResultPosition);

        foreach ($localResultSet as $localResult) {
            $this->assertTrue($localResult instanceof Zend_Service_Yahoo_LocalResult);
        }
    }

    /**
     * Ensures that localSearch() throws an exception when the radius option is invalid
     *
     * @return void
     */
    public function testLocalSearchExceptionRadiusInvalid()
    {
        try {
            $this->_yahoo->localSearch('php', array('zip' => '95014', 'radius' => -1));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('error occurred sending request', $e->getMessage());
        }
    }

    /**
     * Ensures that newsSearch() works as expected when searching for 'php'
     *
     * @return void
     */
    public function testNewsSearchPhp()
    {
        $newsResultSet = $this->_yahoo->newsSearch('php');

        $this->assertTrue($newsResultSet instanceof Zend_Service_Yahoo_NewsResultSet);

        $this->assertTrue($newsResultSet->totalResultsAvailable > 10);
        $this->assertEquals(10, $newsResultSet->totalResultsReturned);
        $this->assertEquals(10, $newsResultSet->totalResults());
        $this->assertEquals(1, $newsResultSet->firstResultPosition);

        foreach ($newsResultSet as $newsResult) {
            $this->assertTrue($newsResult instanceof Zend_Service_Yahoo_NewsResult);
        }
    }

    /**
     * Ensures that pageDataSearch() works as expected given 'http://framework.zend.com/' as a query
     *
     * @return void
     */
    public function testPageDataSearchPhp()
    {
        $pageDataResultSet = $this->_yahoo->pageDataSearch('http://framework.zend.com/');

        $this->assertTrue($pageDataResultSet instanceof Zend_Service_Yahoo_PageDataResultSet);
        $this->assertTrue($pageDataResultSet->totalResultsAvailable > 10);
        $this->assertEquals(50, $pageDataResultSet->totalResultsReturned);
        $this->assertEquals(50, $pageDataResultSet->totalResults());
        $this->assertEquals(1, $pageDataResultSet->firstResultPosition);
        $this->assertEquals(0, $pageDataResultSet->key());

        try {
            $pageDataResultSet->seek(-1);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        foreach ($pageDataResultSet as $pageDataResult) {
            $this->assertTrue($pageDataResult instanceof Zend_Service_Yahoo_PageDataResult);
        }

        $this->assertEquals(50, $pageDataResultSet->key());
        $pageDataResultSet->seek(0);
        $this->assertEquals(0, $pageDataResultSet->key());
    }

    /**
     * Ensures that videoSearch() works as expected given 'php' as a query
     *
     * @return void
     */
    public function testVideoSearchPhp()
    {
        $videoResultSet = $this->_yahoo->videoSearch('php');

        $this->assertTrue($videoResultSet instanceof Zend_Service_Yahoo_VideoResultSet);
        $this->assertTrue($videoResultSet->totalResultsAvailable > 10);
        $this->assertEquals(10, $videoResultSet->totalResultsReturned);
        $this->assertEquals(10, $videoResultSet->totalResults());
        $this->assertEquals(1, $videoResultSet->firstResultPosition);
        $this->assertEquals(0, $videoResultSet->key());

        try {
            $videoResultSet->seek(-1);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        foreach ($videoResultSet as $videoResult) {
            $this->assertTrue($videoResult instanceof Zend_Service_Yahoo_VideoResult);
        }

        $this->assertEquals(10, $videoResultSet->key());
        $videoResultSet->seek(0);
        $this->assertEquals(0, $videoResultSet->key());
    }

    /**
     * Ensures that webSearch() works as expected when searching for 'php'
     *
     * @return void
     */
    public function testWebSearchPhp()
    {
        $webResultSet = $this->_yahoo->webSearch('php');

        $this->assertTrue($webResultSet instanceof Zend_Service_Yahoo_WebResultSet);

        $this->assertTrue($webResultSet->totalResultsAvailable > 10);
        $this->assertEquals(10, $webResultSet->totalResultsReturned);
        $this->assertEquals(10, $webResultSet->totalResults());
        $this->assertEquals(1, $webResultSet->firstResultPosition);

        foreach ($webResultSet as $webResult) {
            $this->assertTrue($webResult instanceof Zend_Service_Yahoo_WebResult);
        }
    }

    /**
     * Ensures that webSearch() throws an exception when the adult_ok option is invalid
     *
     * @return void
     */
    public function testWebSearchExceptionAdultOkInvalid()
    {
        try {
            $this->_yahoo->webSearch('php', array('adult_ok' => 'oops'));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('error occurred sending request', $e->getMessage());
        }
    }

    /**
     * Ensures that webSearch() throws an exception when the similar_ok option is invalid
     *
     * @return void
     */
    public function testWebSearchExceptionSimilarOkInvalid()
    {
        try {
            $this->_yahoo->webSearch('php', array('similar_ok' => 'oops'));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('error occurred sending request', $e->getMessage());
        }
    }

    /**
     * Check support for the region option and ensure that it throws an exception
     * for unsupported regions
     *
     * @group ZF-3222
     * @return void
     */
    public function testWebSearchRegion()
    {
        $this->_yahoo->webSearch('php', array('region' => 'nl'));
        try {
            $this->_yahoo->webSearch('php', array('region' => 'oops'));
            $this->fail('Expected Zend_Service_Exception not thrown');
        }catch (Zend_Service_Exception $e) {
            $this->assertContains("Invalid value for option 'region': oops", $e->getMessage());
        }
    }

    /**
     * Ensures that webSearch() works as expected when searching for 'php'
     *
     * @group ZF-2358
     */
    public function testWebSearchForSite()
    {
        $webResultSet = $this->_yahoo->webSearch('php', array('site' => 'www.php.net'));

        $this->assertTrue($webResultSet instanceof Zend_Service_Yahoo_WebResultSet);

        $this->assertTrue($webResultSet->totalResultsAvailable > 10);
        $this->assertEquals(10, $webResultSet->totalResultsReturned);
        $this->assertEquals(10, $webResultSet->totalResults());
        $this->assertEquals(1, $webResultSet->firstResultPosition);

        foreach ($webResultSet as $webResult) {
            $this->assertTrue($webResult instanceof Zend_Service_Yahoo_WebResult);
        }
    }
}

/**
 * @category   Zend
 * @package    Zend_Service_Yahoo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Yahoo
 */
class Zend_Service_Yahoo_OnlineTest_Skip extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend_Service_Yahoo online tests not enabled with an APPID in TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}
