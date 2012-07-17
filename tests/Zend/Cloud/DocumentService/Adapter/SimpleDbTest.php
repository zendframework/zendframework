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
use Zend\Cloud\DocumentService\Adapter\SimpleDb as AdapterSimpleDb;
use Zend\Cloud\DocumentService\Document;
use Zend\Cloud\DocumentService\Factory;
use Zend\Config;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_DocumentService_Adapter
 * @subpackage UnitTests
 */
class SimpleDbTest extends TestCase
{
    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 10;

    protected $_clientType = 'Zend\Service\Amazon\SimpleDb';

    public function testUpdateDocumentMergeAll()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testMerge");
        $this->_commonDocument->createCollection($name);

        $doc = $this->_makeDocument($data[0]);
        $this->_commonDocument->insertDocument($name, $doc);
        $doc1 = $this->_makeDocument($data[1]);
        $this->_wait();
        $this->_commonDocument->updateDocument($name, $doc->getID(), $doc1,
            array(AdapterSimpleDb::MERGE_OPTION => true));
        $this->_wait();

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc->getID());
        $this->assertTrue($fetchdoc instanceof \Zend\Cloud\DocumentService\Document, "New document not found");
        $this->assertContains($doc->name, $fetchdoc->name, "Name field did not update: " . var_export($fetchdoc->getFields(), 1));
        $this->assertContains($doc1->name, $fetchdoc->name, "Name field did not update: " . var_export($fetchdoc->getFields(), 1));
        $this->assertContains((string) $doc->year, $fetchdoc->year, "Year field did not update: " . var_export($fetchdoc->getFields(), 1));
        $this->assertContains((string) $doc1->year, $fetchdoc->year, "Year field did not update: " . var_export($fetchdoc->getFields(), 1));

        $this->_commonDocument->deleteCollection($name);
    }

    public function testUpdateDocumentMergeSome()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testMerge");
        $this->_commonDocument->createCollection($name);

        $doc = $this->_makeDocument($data[0]);
        $this->_commonDocument->insertDocument($name, $doc);
        $doc1 = $this->_makeDocument($data[1]);
        $this->_wait();
        $this->_commonDocument->updateDocument($name, $doc->getID(), $doc1,
            array(AdapterSimpleDb::MERGE_OPTION =>
                array("year" => true, "pages" => true)));
        $this->_wait();

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc->getID());
        $this->assertTrue($fetchdoc instanceof \Zend\Cloud\DocumentService\Document, "New document not found");
        $this->assertEquals($doc1->name, $fetchdoc->name, "Name field did not update");
        $this->assertContains((string) $doc1->pages, $fetchdoc->pages, "Page field did not update");
        $this->assertContains((string) $doc->pages, $fetchdoc->pages, "Page field did not update");
        $this->assertContains((string) $doc1->year, $fetchdoc->year, "Year field did not update");
        $this->assertContains((string) $doc->year, $fetchdoc->year, "Year field did not update");

        $this->_commonDocument->deleteCollection($name);
    }

    public static function getConfigArray()
    {
        return array(
                Factory::DOCUMENT_ADAPTER_KEY => 'Zend\Cloud\DocumentService\Adapter\SimpleDb',
                AdapterSimpleDb::AWS_ACCESS_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
                AdapterSimpleDb::AWS_SECRET_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY'),
            );
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED') ||
            !constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED') ||
            !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID') ||
            !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')) {
            $this->markTestSkipped("Amazon SimpleDB access not configured, skipping test");
        }

        $config = new Config(self::getConfigArray());

        return $config;
    }

    protected function _getDocumentData()
    {
        return array(
            array(
                parent::ID_FIELD => "0385333498",
                "name" =>	"The Sirens of Titan",
                "author" =>	"Kurt Vonnegut",
                "year"	=> 1959,
                "pages" =>	336,
                "keyword" => array("Book", "Paperback")
                ),
            array(
                parent::ID_FIELD => "0802131786",
                "name" =>	"Tropic of Cancer",
                "author" =>	"Henry Miller",
                "year"	=> 1934,
                "pages" =>	318,
                "keyword" => array("Book")
                ),
            array(
                parent::ID_FIELD => "B000T9886K",
                "name" =>	"In Between",
                "author" =>	"Paul Van Dyk",
                "year"	=> 2007,
                "keyword" => array("CD", "Music")
                ),
            array(
                parent::ID_FIELD => "1579124585",
                "name" =>	"The Right Stuff",
                "author" =>	"Tom Wolfe",
                "year"	=> 1979,
                "pages" =>	304,
                "keyword" => array("American", "Book", "Hardcover")
                ),
        );
    }

    protected function _queryString($domain, $s1, $s2)
    {
        return "select * from $domain where itemName() = '$s1' OR itemName() = '$s2'";
    }

}
