<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendTest\Cloud\DocumentService\Adapter;

use ZendTest\Cloud\DocumentService\TestCase;
use Zend\Cloud\DocumentService\Factory;
use Zend\Cloud\DocumentService\Adapter\WindowsAzure;
use Zend\Config\Config;

/**
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage UnitTests
 */
class WindowsAzureTest extends TestCase
{
    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 10;

    protected $_clientType = 'Zend\Service\WindowsAzure\Storage\Table';

    public function testQueryStructOrder()
    {
        try {
            parent::testQueryStructOrder();
        } catch(\Zend\Cloud\DocumentService\Adapter\Exception\OperationNotAvailableException $e) {
            $this->_commonDocument->deleteCollection($this->_collectionName("testStructQuery4"));
            $this->markTestSkipped('Azure query sorting not implemented yet');
        }
    }

    public static function getConfigArray()
    {
         return array(
            \Zend\Cloud\DocumentService\Factory::DOCUMENT_ADAPTER_KEY => 'Zend\Cloud\DocumentService\Adapter\WindowsAzure',
            \Zend\Cloud\DocumentService\Adapter\WindowsAzure::ACCOUNT_NAME => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTNAME'),
            \Zend\Cloud\DocumentService\Adapter\WindowsAzure::ACCOUNT_KEY => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTKEY'),
            \Zend\Cloud\DocumentService\Adapter\WindowsAzure::HOST => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_TABLE_HOST'),
            \Zend\Cloud\DocumentService\Adapter\WindowsAzure::PROXY_HOST => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_PROXY_HOST'),
            \Zend\Cloud\DocumentService\Adapter\WindowsAzure::PROXY_PORT => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_PROXY_PORT'),
            \Zend\Cloud\DocumentService\Adapter\WindowsAzure::PROXY_CREDENTIALS => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_PROXY_CREDENTIALS'),
        );
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ENABLED') ||
            !constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ENABLED') ||
            !defined('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTNAME') ||
            !defined('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTKEY')) {
            $this->markTestSkipped("Windows Azure access not configured, skipping test");
        }

        $config = new Config(self::getConfigArray());

        return $config;
    }

    protected function _getDocumentData()
    {
        return array(
            array(
                parent::ID_FIELD => array("Amazon", "0385333498"),
                "name" =>	"The Sirens of Titan",
                "author" =>	"Kurt Vonnegut",
                "year"	=> 1959,
                "pages" =>	336,
                "keyword" => "Book"
                ),
            array(
                parent::ID_FIELD => array("Amazon", "0802131786"),
                "name" =>	"Tropic of Cancer",
                "author" =>	"Henry Miller",
                "year"	=> 1934,
                "pages" =>	318,
                "keyword" => "Book"
                ),
            array(
                parent::ID_FIELD => array("Amazon", "B000T9886K"),
                "name" =>	"In Between",
                "author" =>	"Paul Van Dyk",
                "year"	=> 2007,
                "keyword" => "CD"
                ),
           array(
                parent::ID_FIELD => array("Amazon", "1579124585"),
                "name" =>	"The Right Stuff",
                "author" =>	"Tom Wolfe",
                "year"	=> 1979,
                "pages" =>	304,
                "keyword" => "Book"
                ),
        );
    }

    protected function _queryString($domain, $s1, $s2)
    {
        $k1 = $s1[1];
        $k2 = $s2[1];
        return "RowKey eq '$k1' or RowKey eq '$k2'";
    }
}

