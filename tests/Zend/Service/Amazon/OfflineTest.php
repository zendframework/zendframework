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

/**
 * @namespace
 */
namespace ZendTest\Service\Amazon;
use Zend\Service\Amazon;

/**
 * Test helper
 */

/**
 * @see Zend_Service_Amazon
 */

/**
 * @see Zend_Service_Amazon_ResultSet
 */

/**
 * @see Zend_Service_Amazon_ResultSet
 */

/**
 * @see Zend_Http_Client_Adapter_Socket
 */

/**
 * @see Zend\Http\Client\Adapter\Test
 */


/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 */
class OfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Amazon service consumer object
     *
     * @var Zend_Service_Amazon
     */
    protected $_amazon;

    /**
     * HTTP client adapter for testing
     *
     * @var Zend\Http\Client\Adapter\Test
     */
    protected $_httpClientAdapterTest;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        $this->_amazon = new Amazon\Amazon(constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'));

        $this->_httpClientAdapterTest = new \Zend\Http\Client\Adapter\Test();
    }

    /**
     * Ensures that __construct() throws an exception when given an invalid country code
     *
     * @return void
     */
    public function testConstructExceptionCountryCodeInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Exception\InvalidArgumentException',
            'Unknown country code: oops'
        );
        $amazon = new Amazon\Amazon(constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'), 'oops');
    }

    /**
     * @group ZF-2056
     */
    public function testMozardSearchFromFile()
    {
        $xml = file_get_contents(__DIR__."/_files/mozart_result.xml");
        $dom = new \DOMDocument();
        $dom->loadXML($xml);

        $mozartTracks = array(
            'B00005A8JZ' => '29',
            'B0000058HV' => '25',
            'B000BLI3K2' => '500',
            'B00004X0QF' => '9',
            'B000004194' => '19',
            'B00000I9M0' => '9',
            'B000004166' => '20',
            'B00002DEH1' => '58',
            'B0000041EV' => '12',
            'B00004SA87' => '42',
        );

        $result = new Amazon\ResultSet($dom);

        foreach($result AS $item) {
            $trackCount = $mozartTracks[$item->ASIN];
            $this->assertEquals($trackCount, count($item->Tracks));
        }
    }

    /**
     * @group ZF-2749
     */
    public function testSimilarProductConstructorMissingAttributeDoesNotThrowNotice()
    {
        $dom = new \DOMDocument();
        $asin = $dom->createElement("ASIN", "TEST");
        $product = $dom->createElement("product");
        $product->appendChild($asin);

        $similarproduct = new Amazon\SimilarProduct($product);
    }

    /**
     * @group ZF-7251
     */
    public function testFullOffersFromFile()
    {
        $xml = file_get_contents(__DIR__."/_files/offers_with_names.xml");
        $dom = new \DOMDocument();
        $dom->loadXML($xml);

        $dataExpected = array(
            '0439774098' => array(
                'offers' => array(
                    'A79CLRHOQ3NF4' => array(
                        'name'  => 'PLEXSUPPLY',
                        'price' => '5153'
                    ),
                    'A2K9NS8DSVOE2W' => array(
                        'name'  => 'nangsuer',
                        'price' => '5153'
                    ),
                    'A31EVTLIC13ORD' => array(
                        'name'  => 'Wizard of Math',
                        'price' => '7599'
                    ),
                    'A3SKJE188CW5XG' => array(
                        'name'  => 'ReStockIt',
                        'price' => '5299'
                    ),
                    'A1729W3053T57N' => array(
                        'name'  => 'The Price Pros',
                        'price' => '5487'
                    ),
                    'A29PHU0KPCGV8S' => array(
                        'name'  => 'TheFactoryDepot',
                        'price' => '5821'
                    ),
                    'AIHRRFGW11GJ8' => array(
                        'name'  => 'Design Tec Office Products',
                        'price' => '5987'
                    ),
                    'A27OK403WRHSGI' => array(
                        'name'  => 'Kaplan Early Learning Company',
                        'price' => '7595'
                    ),
                    'A25DVOZOPBFMAN' => array(
                        'name'  => 'Deerso',
                        'price' => '7599'
                    ),
                    'A6IFKC796Y64H' => array(
                        'name'  => 'The Education Station Inc',
                        'price' => '7599'
                    ),
                ),
            ),
            'B00000194U' => array(
                'offers' => array(
                    'A3UOG6723G7MG0' => array(
                        'name'  => 'Efunctional',
                        'price' => '480'
                    ),
                    'A3SNNXCKUIW1O2' => array(
                        'name'  => 'Universal Mania',
                        'price' => '531'
                    ),
                    'A18ACDNYOEMMOL' => array(
                        'name'  => 'ApexSuppliers',
                        'price' => '589'
                    ),
                    'A2NYACAJP9I1IY' => array(
                        'name'  => 'GizmosForLife',
                        'price' => '608'
                    ),
                    'A1729W3053T57N' => array(
                        'name'  => 'The Price Pros',
                        'price' => '628'
                    ),
                    'A29PHU0KPCGV8S' => array(
                        'name'  => 'TheFactoryDepot',
                        'price' => '638'
                    ),
                    'A3Q3IAIX1CLBMZ' => array(
                        'name'  => 'ElectroGalaxy',
                        'price' => '697'
                    ),
                    'A1PC5XI7QQLW5G' => array(
                        'name'  => 'Long Trading Company',
                        'price' => '860'
                    ),
                    'A2R0FX412W1BDT' => array(
                        'name'  => 'Beach Audio',
                        'price' => '896'
                    ),
                    'AKJJGJ0JKT8F1' => array(
                        'name'  => 'Buy.com',
                        'price' => '899'
                    ),
                ),
            ),
        );

        $result = new Amazon\ResultSet($dom);

        foreach($result AS $item) {
            $data = $dataExpected[$item->ASIN];
            foreach($item->Offers->Offers as $offer) {
                $this->assertEquals($data['offers'][$offer->MerchantId]['name'], $offer->MerchantName);
                $this->assertEquals($data['offers'][$offer->MerchantId]['price'], $offer->Price);
            }
        }
    }

    public function dataSignatureEncryption()
    {
        return array(
            array(
                'http://webservices.amazon.com',
                array(
                    'Service' => 'AWSECommerceService',
                    'AWSAccessKeyId' => '00000000000000000000',
                    'Operation' => 'ItemLookup',
                    'ItemId' => '0679722769',
                    'ResponseGroup' => 'ItemAttributes,Offers,Images,Reviews',
                    'Version' => '2009-01-06',
                    'Timestamp' => '2009-01-01T12:00:00Z',
                ),
                "GET\n".
                "webservices.amazon.com\n".
                "/onca/xml\n".
                "AWSAccessKeyId=00000000000000000000&ItemId=0679722769&Operation=I".
                "temLookup&ResponseGroup=ItemAttributes%2COffers%2CImages%2CReview".
                "s&Service=AWSECommerceService&Timestamp=2009-01-01T12%3A00%3A00Z&".
                "Version=2009-01-06",
                'Nace%2BU3Az4OhN7tISqgs1vdLBHBEijWcBeCqL5xN9xg%3D'
            ),
            array(
                'http://ecs.amazonaws.co.uk',
                array(
                    'Service' => 'AWSECommerceService',
                    'AWSAccessKeyId' => '00000000000000000000',
                    'Operation' => 'ItemSearch',
                    'Actor' => 'Johnny Depp',
                    'ResponseGroup' => 'ItemAttributes,Offers,Images,Reviews,Variations',
                    'Version' => '2009-01-01',
                    'SearchIndex' => 'DVD',
                    'Sort' => 'salesrank',
                    'AssociateTag' => 'mytag-20',
                    'Timestamp' => '2009-01-01T12:00:00Z',
                ),
                "GET\n".
                "ecs.amazonaws.co.uk\n".
                "/onca/xml\n".
                "AWSAccessKeyId=00000000000000000000&Actor=Johnny%20Depp&Associate".
                "Tag=mytag-20&Operation=ItemSearch&ResponseGroup=ItemAttributes%2C".
                "Offers%2CImages%2CReviews%2CVariations&SearchIndex=DVD&Service=AW".
                "SECommerceService&Sort=salesrank&Timestamp=2009-01-01T12%3A00%3A0".
                "0Z&Version=2009-01-01",
                'TuM6E5L9u%2FuNqOX09ET03BXVmHLVFfJIna5cxXuHxiU%3D',
            ),
        );
    }

    /**
     * Checking if signature Encryption due on August 15th for Amazon Webservice API is working correctly.
     *
     * @dataProvider dataSignatureEncryption
     * @group ZF-7033
     */
    public function testSignatureEncryption($baseUri, $params, $expectedStringToSign, $expectedSignature)
    {
        $this->assertEquals(
            $expectedStringToSign,
            Amazon\Amazon::buildRawSignature($baseUri, $params)
        );

        $this->assertEquals(
            $expectedSignature,
            rawurlencode(Amazon\Amazon::computeSignature(
                $baseUri, '1234567890', $params
            ))
        );
    }
    
	/**
     * Testing if Amazon service component can handle return values where the
     * item-list is not empty
     * 
     * @group ZF-9547
     */
    public function testAmazonComponentHandlesValidBookResults()
    {
    	$xml = file_get_contents(__DIR__."/_files/amazon-response-valid.xml");
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        
    	$result = new Amazon\ResultSet($dom);

    	$currentItem = null;
    	try {
    		$currentItem = $result->current();
    	} catch (Amazon\Exception $e) {
    		$this->fail('Unexpected exception was triggered');
    	}
    	$this->assertInstanceOf('Zend\Service\Amazon\Item', $currentItem);
    	$this->assertEquals('0754512673', $currentItem->ASIN);
    }
    
    /**
     * Testing if Amazon service component can handle return values where the
     * item-list is empty (no results found)
     * 
     * @group ZF-9547
     */
    public function testAmazonComponentHandlesEmptyBookResults()
    {
    	$xml = file_get_contents(__DIR__."/_files/amazon-response-invalid.xml");
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        
    	$result = new Amazon\ResultSet($dom);

    	try {
    		$result->current();
    		$this->fail('Expected exception was not triggered');
    	} catch (Amazon\Exception $e) {
			return;
        }
    }
}
