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
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Amazon;

use Zend\Service\Amazon;
use Zend\Service;

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Amazon service consumer object
     *
     * @var Zend_Service_Amazon
     */
    protected $_amazon;

    /**
     * Reference to Amazon query API object
     *
     * @var Zend_Service_Amazon_Query
     */
    protected $_query;

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
        if (!constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_Service_Amazon_S3 online tests are not enabled');
        }
        if(!defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID') || !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')) {
            $this->markTestSkipped('Constants AccessKeyId and SecretKey have to be set.');
        }

        $this->_amazon = new Amazon\Amazon(
            TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID,
            'US',
            TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY
        );

        $this->_query = new Amazon\Query(
            TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID,
            'US',
            TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY
        );

        $this->_httpClientAdapterSocket = new \Zend\Http\Client\Adapter\Socket();

        $this->_amazon->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterSocket);

        // terms of use compliance: no more than one query per second
        sleep(1);
    }

    public function testUnknownCountryException()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Exception\InvalidArgumentException', 
            'Unknown country code: wrong-country-code');
        $aws = new Amazon\Amazon(
            TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID, 
            'wrong-country-code', 
            TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY);
    }

    /**
     * Ensures that itemSearch() works as expected when searching for PHP books
     * @group ItemSearchPhp
     * @return void
     */
    public function testItemSearchBooksPhp()
    {
        $resultSet = $this->_amazon->itemSearch(array(
            'SearchIndex'   => 'Books',
            'Keywords'      => 'php',
            'ResponseGroup' => 'Small,ItemAttributes,Images,SalesRank,Reviews,EditorialReview,Similarities,'
                             . 'ListmaniaLists'
            ));

        $this->assertTrue(10 < $resultSet->totalResults());
        $this->assertTrue(1 < $resultSet->totalPages());
        $this->assertEquals(0, $resultSet->key());

        try {
            $resultSet->seek(-1);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (\OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->seek(9);

        try {
            $resultSet->seek(10);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (\OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        foreach ($resultSet as $item) {
            $this->assertTrue($item instanceof Amazon\Item);
        }

        $this->assertTrue(simplexml_load_string($item->asXml()) instanceof \SimpleXMLElement);
    }

    /**
     * Ensures that itemSearch() works as expected when searching for music with keyword of Mozart
     *
     * @return void
     */
    public function testItemSearchMusicMozart()
    {
        $resultSet = $this->_amazon->itemSearch(array(
            'SearchIndex'   => 'Music',
            'Keywords'      => 'Mozart',
            'ResponseGroup' => 'Small,Tracks,Offers'
            ));

        foreach ($resultSet as $item) {
            $this->assertTrue($item instanceof Amazon\Item);
        }
    }

    /**
     * Ensures that itemSearch() works as expected when searching for digital cameras
     *
     * @return void
     */
    public function testItemSearchElectronicsDigitalCamera()
    {
        $resultSet = $this->_amazon->itemSearch(array(
            'SearchIndex'   => 'Electronics',
            'Keywords'      => 'digital camera',
            'ResponseGroup' => 'Accessories'
            ));

        foreach ($resultSet as $item) {
            $this->assertTrue($item instanceof Amazon\Item);
        }
    }

    /**
     * Ensures that itemSearch() works as expected when sorting
     *
     * @return void
     */
    public function testItemSearchBooksPHPSort()
    {
        $resultSet = $this->_amazon->itemSearch(array(
            'SearchIndex' => 'Books',
            'Keywords'    => 'php',
            'Sort'        => '-titlerank'
            ));

        foreach ($resultSet as $item) {
            $this->assertTrue($item instanceof Amazon\Item);
        }
    }

    /**
     * Ensures that itemSearch() throws an exception when provided an invalid city
     *
     * @return void
     */
    public function testItemSearchExceptionCityInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Exception\RuntimeException', 
            'The value you specified for SearchIndex is invalid.'
        );
        $this->_amazon->itemSearch(array(
            'SearchIndex' => 'Restaurants',
            'Keywords'    => 'seafood',
            'City'        => 'Des Moines'
            ));
    }

    /**
     * Ensures that itemLookup() works as expected
     *
     * @return void
     */
    public function testItemLookup()
    {
        $item = $this->_amazon->itemLookup('B0015T963C');
        $this->assertTrue($item instanceof Amazon\Item);
    }

    /**
     * Ensures that itemLookup() throws an exception when provided an invalid ASIN
     *
     * @return void
     */
    public function testItemLookupExceptionAsinInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Exception\RuntimeException',
            'OOPS is not a valid value for ItemId. Please change this value and retry your request. (AWS.InvalidParameterValue)'
        );
        $this->_amazon->itemLookup('oops');
    }

    /**
     * Ensures that itemLookup() works as expected when provided multiple ASINs
     *
     * @return void
     */
    public function testItemLookupMultiple()
    {
        $resultSet = $this->_amazon->itemLookup('0596006810,1590593804');

        $count = 0;
        foreach ($resultSet as $item) {
            $this->assertTrue($item instanceof Amazon\Item);
            $count++;
        }

        $this->assertEquals(2, $count);
    }

    /**
     * Ensures that itemLookup() throws an exception when given a SearchIndex
     *
     * @return void
     */
    public function testItemLookupExceptionSearchIndex()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Exception\RuntimeException',
            'Your request contained a restricted parameter combination.  When IdType equals ASIN, SearchIndex cannot be present.'
        );
        $this->_amazon->itemLookup('oops', array('SearchIndex' => 'Books'));
    }

    /**
     * Ensures that the query API works as expected when searching for PHP books
     *
     * @return void
     */
    public function testQueryBooksPhp()
    {
        $resultSet = $this->_query->category('Books')->Keywords('php')->search();

        foreach ($resultSet as $item) {
            $this->assertTrue($item instanceof Amazon\Item);
        }
    }

    /**
     * Ensures that the query API throws an exception when a category is not first provided
     *
     * @return void
     */
    public function testQueryExceptionCategoryMissing()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Exception\RuntimeException',
            'You must set a category before setting the search parameters'
        );
        $this->_query->Keywords('php');
    }

    /**
     * Ensures that the query API throws an exception when the category is invalid
     *
     * @return void
     */
    public function testQueryExceptionCategoryInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Exception\RuntimeException',
            'The value you specified for SearchIndex is invalid.'
        );
        $this->_query->category('oops')->search();
    }

    /**
     * Ensures that the query API works as expected when searching by ASIN
     *
     * @return void
     */
    public function testQueryAsin()
    {
        $item = $this->_query->asin('B0015T963C')->search();
        $this->assertTrue($item instanceof Amazon\Item);
    }
}


/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 */
class Skip extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend_Service_Amazon online tests not enabled with an access key ID in '
                             . 'TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}
