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
 * @package    Zend\Service\Amazon\SimpleDb
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Service\Amazon\SimpleDb;

use Zend\Service\Amazon\SimpleDb;
use Zend\Service\Amazon\SimpleDb\Exception;
use Zend\Http\Client\Adapter\Socket;


/**
 * @category   Zend
 * @package    Zend\Service\Amazon\SimpleDb
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Amazon service consumer object
     *
     * @var Zend\Service\Amazon\SimpleDb
     */
    protected $_amazon;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend\Http\Client\Adapter\Socket
     */
    protected $_httpClientAdapterSocket;

    protected $_testDomainNamePrefix;

    protected $_testItemNamePrefix;

    protected $_testAttributeNamePrefix;

    // Because Amazon uses an eventual consistency model, this test period may
    // help avoid *but not guarantee* false negatives
    protected $_testWaitPeriod = 2;

    /**
     * Maximum attempts performed in request()
     *
     * @var int
     */
    protected $_testWaitRetries = 3;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_Service_Amazon online tests are not enabled');
        }

        $this->_amazon = new SimpleDb\SimpleDb(
            constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
            constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
        );

        $this->_httpClientAdapterSocket = new Socket();

        $this->_amazon->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterSocket);

        $this->_testDomainNamePrefix = 'TestsZendServiceAmazonSimpleDbDomain';

        $this->_testItemNamePrefix = 'TestsZendServiceAmazonSimpleDbItem';

        $this->_testAttributeNamePrefix = 'TestsZendServiceAmazonSimpleDbAttribute';

        $this->_wait();
    }

    /**
     * Wrapper around remote calls to retry, apply wait, etc.
     *
     * @param string $method SimpleDB method name
     * @param array $args Method argument list
     * @return void
     */
    public function request($method, $args = array())
    {
        $response = null;
        for ($try = 1; $try <= $this->_testWaitRetries; $try++) {
            try {
                $this->_wait();
                $response = call_user_func_array(array($this->_amazon, $method), $args);
                break;
            } catch (Zend_Service_Amazon_SimpleDb_Exception $e) {
                // Only retry after throtte-related error
                if (false === strpos($e->getMessage(), 'currently unavailable')) {
                    throw $e;
                }
            }
        }
        return $response;
    }

    public function testGetAttributes() {
        $domainName = $this->_testDomainNamePrefix . '_testGetAttributes';
        $this->request('deleteDomain', array($domainName));
        $this->request('createDomain', array($domainName));
        try {
            $itemName = $this->_testItemNamePrefix . '_testGetAttributes';
            $attributeName1 = $this->_testAttributeNamePrefix . '_testGetAttributes1';
            $attributeName2 = $this->_testAttributeNamePrefix . '_testGetAttributes2';
            $attributeValue1 = 'value1';
            $attributeValue2 = 'value2';
            $attributes = array(
                $attributeName1 => new SimpleDb\Attribute($itemName, $attributeName1, $attributeValue1),
                $attributeName2 => new SimpleDb\Attribute($itemName, $attributeName2, $attributeValue2)
            );

            // Now that everything's set up, test it
            $this->request('putAttributes', array($domainName, $itemName, $attributes));

            // One attribute
            $results = $this->request('getAttributes', array($domainName, $itemName, $attributeName1));
            $this->assertEquals(1, count($results));
            $attribute = current($results);
            $this->assertEquals($attributeName1, $attribute->getName());
            $this->assertEquals($attributeValue1, current($attribute->getValues()));

            // Multiple attributes
            $results = $this->request('getAttributes', array($domainName, $itemName));
            $this->assertEquals(2, count($results));
            $this->assertTrue(array_key_exists($attributeName1, $results));
            $this->assertTrue(array_key_exists($attributeName2, $results));
            $this->assertEquals($attributeValue1, current($results[$attributeName1]->getValues()));
            $this->assertEquals($attributeValue2, current($results[$attributeName2]->getValues()));

            $this->request('deleteDomain', array($domainName));
        } catch(Exception $e) {
            $this->request('deleteDomain', array($domainName));
            throw $e;
        }
    }

    public function testPutAttributes() {
        $domainName = $this->_testDomainNamePrefix . '_testPutAttributes';
        $this->request('deleteDomain', array($domainName));
        $this->request('createDomain', array($domainName));
        try {
            $itemName = $this->_testItemNamePrefix . '_testPutAttributes';
            $attributeName1 = $this->_testAttributeNamePrefix . '_testPutAttributes1';
            $attributeName2 = $this->_testAttributeNamePrefix . '_testPutAttributes2';
            $attributeValue1 = 'value1';
            $attributeValue2 = 'value2';
            $attributes = array(
                $attributeName1 => new SimpleDb\Attribute($itemName, $attributeName1, $attributeValue1),
                $attributeName2 => new SimpleDb\Attribute($itemName, $attributeName2, $attributeValue2)
            );

            // Now that everything's set up, test it
            $this->request('putAttributes', array($domainName, $itemName, $attributes));

            // Multiple attributes
            $results = $this->request('getAttributes', array($domainName, $itemName));
            $this->assertEquals(2, count($results));
            $this->assertTrue(array_key_exists($attributeName1, $results));
            $this->assertTrue(array_key_exists($attributeName2, $results));
            $this->assertEquals($attributes[$attributeName1], $results[$attributeName1]);
            $this->assertEquals($attributes[$attributeName2], $results[$attributeName2]);
            $this->request('deleteDomain', array($domainName));
        } catch(Exception $e) {
            $this->request('deleteDomain', array($domainName));
            throw $e;
        }
    }

    public function testBatchPutAttributes() {
        $domainName = $this->_testDomainNamePrefix . '_testBatchPutAttributes';
        $this->request('deleteDomain', array($domainName));
        $this->request('createDomain', array($domainName));
        try {
            $itemName1 = $this->_testItemNamePrefix . '_testBatchPutAttributes1';
            $itemName2 = $this->_testItemNamePrefix . '_testBatchPutAttributes2';
            $attributeName1 = $this->_testAttributeNamePrefix . '_testBatchPutAttributes1';
            $attributeName2 = $this->_testAttributeNamePrefix . '_testBatchPutAttributes2';
            $attributeName3 = $this->_testAttributeNamePrefix . '_testBatchPutAttributes3';
            $attributeName4 = $this->_testAttributeNamePrefix . '_testBatchPutAttributes4';
            $attributeValue1 = 'value1';
            $attributeValue2 = 'value2';
            $attributeValue3 = 'value3';
            $attributeValue4 = 'value4';
            $attributeValue5 = 'value5';
            $items = array(
                $itemName1 => array(
                    $attributeName1 => new SimpleDb\Attribute($itemName1, $attributeName1, $attributeValue1),
                    $attributeName2 => new SimpleDb\Attribute($itemName1, $attributeName2, $attributeValue2)),
                $itemName2 => array(
                    $attributeName3 => new SimpleDb\Attribute($itemName2, $attributeName3, $attributeValue3),
                    $attributeName4 => new SimpleDb\Attribute($itemName2, $attributeName4, array($attributeValue4, $attributeValue5)))
                );


            $replace = array(
                $itemName1 => array(
                    $attributeName1 => false,
                    $attributeName2 => false
                ),
                $itemName2 => array(
                    $attributeName3 => false,
                    $attributeName4 => false
                )
            );

            $this->assertEquals(array(), $this->request('getAttributes', array($domainName, $itemName1)));
            $this->request('batchPutAttributes', array($items, $domainName, $replace));

            $result = $this->request('getAttributes', array($domainName, $itemName1, $attributeName1));

            $this->assertTrue(array_key_exists($attributeName1, $result));
            $this->assertEquals($attributeName1, $result[$attributeName1]->getName());
            $this->assertEquals($attributeValue1, current($result[$attributeName1]->getValues()));
            $result = $this->request('getAttributes', array($domainName, $itemName2, $attributeName4));
            $this->assertTrue(array_key_exists($attributeName4, $result));
            $this->assertEquals(2, count($result[$attributeName4]->getValues()));
            $result = $this->request('getAttributes', array($domainName, $itemName2));
            $this->assertEquals(2, count($result));
            $this->assertTrue(array_key_exists($attributeName3, $result));
            $this->assertEquals($attributeName3, $result[$attributeName3]->getName());
            $this->assertEquals(1, count($result[$attributeName3]));
            $this->assertEquals($attributeValue3, current($result[$attributeName3]->getValues()));
            $this->assertTrue(array_key_exists($attributeName4, $result));
            $this->assertEquals($attributeName4, $result[$attributeName4]->getName());
            $this->assertEquals(2, count($result[$attributeName4]->getValues()));
            $this->assertEquals(array($attributeValue4, $attributeValue5), $result[$attributeName4]->getValues());

            // Test replace
            $newAttributeValue1 = 'newValue1';
            $newAttributeValue4 = 'newValue4';
            $items[$itemName1][$attributeName1]->setValues(array($newAttributeValue1));
            $items[$itemName2][$attributeName4]->setValues(array($newAttributeValue4));

            $this->request('batchPutAttributes', array($items, $domainName, $replace));

            $result = $this->request('getAttributes', array($domainName, $itemName1, $attributeName1));
            $this->assertEquals(array($newAttributeValue1, $attributeValue1), $result[$attributeName1]->getValues());

            $result = $this->request('getAttributes', array($domainName, $itemName2, $attributeName4));
            $this->assertEquals(array($newAttributeValue4, $attributeValue4, $attributeValue5), $result[$attributeName4]->getValues());

            $replace[$itemName1][$attributeName1] = true;
            $replace[$itemName2][$attributeName4] = true;

            $this->request('batchPutAttributes', array($items, $domainName, $replace));

            $result = $this->request('getAttributes', array($domainName, $itemName1, $attributeName1));
            $this->assertEquals($items[$itemName1][$attributeName1], $result[$attributeName1]);

            $result = $this->request('getAttributes', array($domainName, $itemName2, $attributeName4));
            $this->assertEquals($items[$itemName2][$attributeName4], $result[$attributeName4]);
            $this->assertEquals($items[$itemName1], $this->request('getAttributes', array($domainName, $itemName1)));

            $this->request('deleteDomain', array($domainName));
        } catch(Exception $e) {
            $this->request('deleteDomain', array($domainName));
            throw $e;
        }
    }

    public function testDeleteAttributes() {
        $domainName = $this->_testDomainNamePrefix . '_testDeleteAttributes';
        $this->request('deleteDomain', array($domainName));
        $this->request('createDomain', array($domainName));
        try {
            $itemName = $this->_testItemNamePrefix . '_testDeleteAttributes';
            $attributeName1 = $this->_testAttributeNamePrefix . '_testDeleteAttributes1';
            $attributeName2 = $this->_testAttributeNamePrefix . '_testDeleteAttributes2';
            $attributeName3 = $this->_testAttributeNamePrefix . '_testDeleteAttributes3';
            $attributeName4 = $this->_testAttributeNamePrefix . '_testDeleteAttributes4';
            $attributeValue1 = 'value1';
            $attributeValue2 = 'value2';
            $attributeValue3 = 'value3';
            $attributeValue4 = 'value4';
            $attributes = array(
                new SimpleDb\Attribute($itemName, $attributeName1, $attributeValue1),
                new SimpleDb\Attribute($itemName, $attributeName2, $attributeValue2),
                new SimpleDb\Attribute($itemName, $attributeName3, $attributeValue3),
                new SimpleDb\Attribute($itemName, $attributeName4, $attributeValue4)
            );

            // Now that everything's set up, test it
            $this->request('putAttributes', array($domainName, $itemName, $attributes));

            $results = $this->request('getAttributes', array($domainName, $itemName));
            $this->assertEquals(4, count($results));
            $this->assertTrue(array_key_exists($attributeName1, $results));
            $this->assertTrue(array_key_exists($attributeName2, $results));
            $this->assertTrue(array_key_exists($attributeName3, $results));
            $this->assertTrue(array_key_exists($attributeName4, $results));
            $this->assertEquals($attributeValue1, current($results[$attributeName1]->getValues()));
            $this->assertEquals($attributeValue2, current($results[$attributeName2]->getValues()));
            $this->assertEquals($attributeValue3, current($results[$attributeName3]->getValues()));
            $this->assertEquals($attributeValue4, current($results[$attributeName4]->getValues()));

            $this->request('deleteAttributes', array($domainName, $itemName, array($attributes[0])));

            $results = $this->request('getAttributes', array($domainName, $itemName));
            $this->assertEquals(3, count($results));
            $this->assertTrue(array_key_exists($attributeName2, $results));
            $this->assertTrue(array_key_exists($attributeName3, $results));
            $this->assertTrue(array_key_exists($attributeName4, $results));
            $this->assertEquals($attributeValue2, current($results[$attributeName2]->getValues()));
            $this->assertEquals($attributeValue3, current($results[$attributeName3]->getValues()));
            $this->assertEquals($attributeValue4, current($results[$attributeName4]->getValues()));

            $this->request('deleteAttributes', array($domainName, $itemName, array($attributes[1], $attributes[2])));

            $results = $this->request('getAttributes', array($domainName, $itemName));
            $this->assertEquals(1, count($results));
            $this->assertTrue(array_key_exists($attributeName4, $results));
            $this->assertEquals($attributeValue4, current($results[$attributeName4]->getValues()));


            $this->request('deleteAttributes', array($domainName, $itemName, array($attributes[3])));

            $results = $this->request('getAttributes', array($domainName, $itemName));
            $this->assertEquals(0, count($results));

            $this->request('deleteDomain', array($domainName));
        } catch(Exception $e) {
            $this->request('deleteDomain', array($domainName));
            throw $e;
        }
    }

    /**
     *
     * @param $maxNumberOfDomains Integer between 1 and 100
     * @param $nextToken          Integer between 1 and 100
     * @return array              0 or more domain names
     */
    public function testListDomains() {
        $domainName = null;
        try {
            // Create some domains
            for($i = 1; $i <= 3; $i++) {
                $domainName = $this->_testDomainNamePrefix . '_testListDomains' . $i;
                $this->request('deleteDomain', array($domainName));
                $this->request('createDomain', array($domainName));
            }

            $page = $this->request('listDomains', array(3));
            $this->assertEquals(3, count($page->getData()));
            // Amazon returns an empty page as the last page :/
            $isLast = $page->isLast();
            if (!$isLast) {
              // The old isLast() assertTrue failed in full suite runs. Token often
              // decodes to 'TestsZendServiceAmazonSimpleDbDomain_testPutAttributes'
              // which no longer exists. Instead of a plain assertTrue, which seemed
              // to pass only in single-case runs, we'll make sure the token's
              // presence is worth a negative.
              $token = $page->getToken();
              if ($token) {
                $tokenDomainName = base64_decode($token);
                if (false !== strpos($tokenDomainName, $this->_testDomainNamePrefix)) {
                  try {
                    $this->request('domainMetadata', array($tokenDomainName));
                    $this->fail('listDomains call with 3 domain maximum did not return last page');
                  } catch (Exception $e) {
                    $this->assertContains('The specified domain does not exist', $e->getMessage());
                  }
                }
              }
            }
            $this->assertEquals(1, count($this->request('listDomains', array(1, $page->getToken()))));

            $page = $this->request('listDomains', array(4));
            $this->assertEquals(3, count($page->getData()));
            $this->assertTrue($page->isLast());

            $page = $this->request('listDomains', array(2));
            $this->assertEquals(2, count($page->getData()));
            $this->assertFalse($page->isLast());

            $nextPage = $this->request('listDomains', array(100, $page->getToken()));
            $this->assertEquals(1, count($nextPage->getData()));
            // Amazon returns an empty page as the last page :/
            $this->assertTrue($nextPage->isLast());

            // Delete the domains
            for($i = 1; $i <= 3; $i++) {
                $domainName = $this->_testDomainNamePrefix . '_testListDomains' . $i;
                $this->request('deleteDomain', array($domainName));
            }
        } catch(Exception $e) {
            // Delete the domains
            for($i = 1; $i <= 3; $i++) {
                $domainName = $this->_testDomainNamePrefix . '_testListDomains' . $i;
                $this->request('deleteDomain', array($domainName));
            }
            throw $e;
        }
    }

    /**
     * @param $domainName string Name of the domain for which metadata will be requested
     * @return array Key/value array of metadatum names and values.
     */
    public function testDomainMetadata() {
        $domainName = $this->_testDomainNamePrefix . '_testDomainMetadata';
        $this->request('deleteDomain', array($domainName));
        $this->request('createDomain', array($domainName));
        try {
            $metadata = $this->request('domainMetadata', array($domainName));
            $this->assertTrue(is_array($metadata));
            $this->assertGreaterThan(0, count($metadata));
            $this->assertTrue(array_key_exists('ItemCount', $metadata));
            $this->assertEquals(0, (int)$metadata['ItemCount']);
            $this->assertTrue(array_key_exists('ItemNamesSizeBytes', $metadata));
            $this->assertEquals(0, (int)$metadata['ItemNamesSizeBytes']);
            $this->assertTrue(array_key_exists('AttributeNameCount', $metadata));
            $this->assertEquals(0, (int)$metadata['AttributeNameCount']);
            $this->assertTrue(array_key_exists('AttributeValueCount', $metadata));
            $this->assertEquals(0, (int)$metadata['AttributeValueCount']);
            $this->assertTrue(array_key_exists('AttributeNamesSizeBytes', $metadata));
            $this->assertEquals(0, (int)$metadata['AttributeNamesSizeBytes']);
            $this->assertTrue(array_key_exists('AttributeValuesSizeBytes', $metadata));
            $this->assertEquals(0, (int)$metadata['AttributeValuesSizeBytes']);
            $this->assertTrue(array_key_exists('Timestamp', $metadata));

            // Delete the domain
            $this->request('deleteDomain', array($domainName));
        } catch(Exception $e) {
            $this->request('deleteDomain', array($domainName));
            throw $e;
        }
    }

    /**
     *
     * @param $domainName	string	Valid domain name of the domain to create
     * @return 				boolean True if successful, false if not
     */
	public function testCreateDomain() {
	    $domainName = $this->_testDomainNamePrefix . '_testCreateDomain';
        $this->request('deleteDomain', array($domainName));
        $this->request('createDomain', array($domainName));
        try {
            $domainListPage = $this->request('listDomains');
            $this->assertContains($domainName, $domainListPage->getData());
            // Delete the domain
            $this->request('deleteDomain', array($domainName));
        } catch(Exception $e) {
            $this->request('deleteDomain', array($domainName));
            throw $e;
        }
    }

	public function testDeleteDomain() {
	    $domainName = $this->_testDomainNamePrefix . '_testDeleteDomain';
        $this->request('deleteDomain', array($domainName));
        $this->request('createDomain', array($domainName));
        try {
            $domainListPage = $this->request('listDomains');
            $this->assertContains($domainName, $domainListPage->getData());
            $this->assertNull($domainListPage->getToken());
            // Delete the domain
            $this->request('deleteDomain', array($domainName));
            $domainListPage = $this->request('listDomains');
            $this->assertNotContains($domainName, $domainListPage->getData());
        } catch(Exception $e) {
            $this->request('deleteDomain', array($domainName));
            throw $e;
        }
    }

    private function _wait() {
        sleep($this->_testWaitPeriod);
    }

    /**
     * Tear down the test case
     *
     * @return void
     */
    public function tearDown()
    {

        // $this->request('deleteDomain', array($this->_testDomainNamePrefix));
        // Delete domains

        unset($this->_amazon);
    }
}
