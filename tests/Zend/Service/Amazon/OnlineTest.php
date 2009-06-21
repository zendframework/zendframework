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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Service_Amazon
 */
require_once 'Zend/Service/Amazon.php';

/**
 * @see Zend_Service_Amazon_Query
 */
require_once 'Zend/Service/Amazon/Query.php';

/**
 * @see Zend_Http_Client_Adapter_Socket
 */
require_once 'Zend/Http/Client/Adapter/Socket.php';


/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_OnlineTest extends PHPUnit_Framework_TestCase
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
        $this->_amazon = new Zend_Service_Amazon(constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'));

        $this->_query = new Zend_Service_Amazon_Query(constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'));

        $this->_httpClientAdapterSocket = new Zend_Http_Client_Adapter_Socket();

        $this->_amazon->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterSocket);

        // terms of use compliance: no more than one query per second
        sleep(1);
    }

    /**
     * Ensures that itemSearch() works as expected when searching for PHP books
     *
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

        foreach ($resultSet as $item) {
            $this->assertTrue($item instanceof Zend_Service_Amazon_Item);
        }

        $this->assertTrue(simplexml_load_string($item->asXml()) instanceof SimpleXMLElement);
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
            $this->assertTrue($item instanceof Zend_Service_Amazon_Item);
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
            $this->assertTrue($item instanceof Zend_Service_Amazon_Item);
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
            $this->assertTrue($item instanceof Zend_Service_Amazon_Item);
        }
    }

    /**
     * Ensures that itemSearch() throws an exception when provided an invalid city
     *
     * @return void
     */
    public function testItemSearchExceptionCityInvalid()
    {
        try {
            $this->_amazon->itemSearch(array(
                'SearchIndex' => 'Restaurants',
                'Keywords'    => 'seafood',
                'City'        => 'Des Moines'
                ));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
        }
    }

    /**
     * Ensures that itemLookup() works as expected
     *
     * @return void
     */
    public function testItemLookup()
    {
        $item = $this->_amazon->itemLookup('B0000A432X');
        $this->assertTrue($item instanceof Zend_Service_Amazon_Item);
    }

    /**
     * Ensures that itemLookup() throws an exception when provided an invalid ASIN
     *
     * @return void
     */
    public function testItemLookupExceptionAsinInvalid()
    {
        try {
            $this->_amazon->itemLookup('oops');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('not a valid value for ItemId', $e->getMessage());
        }
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
            $this->assertTrue($item instanceof Zend_Service_Amazon_Item);
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
        try {
            $this->_amazon->itemLookup('oops', array('SearchIndex' => 'Books'));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('restricted parameter combination', $e->getMessage());
        }
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
            $this->assertTrue($item instanceof Zend_Service_Amazon_Item);
        }
    }

    /**
     * Ensures that the query API throws an exception when a category is not first provided
     *
     * @return void
     */
    public function testQueryExceptionCategoryMissing()
    {
        try {
            $this->_query->Keywords('php');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('set a category', $e->getMessage());
        }
    }

    /**
     * Ensures that the query API throws an exception when the category is invalid
     *
     * @return void
     */
    public function testQueryExceptionCategoryInvalid()
    {
        try {
            $this->_query->category('oops')->search();
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('SearchIndex is invalid', $e->getMessage());
        }
    }

    /**
     * Ensures that the query API works as expected when searching by ASIN
     *
     * @return void
     */
    public function testQueryAsin()
    {
        $item = $this->_query->asin('B0000A432X')->search();
        $this->assertTrue($item instanceof Zend_Service_Amazon_Item);
    }
}


class Zend_Service_Amazon_OnlineTest_Skip extends PHPUnit_Framework_TestCase
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