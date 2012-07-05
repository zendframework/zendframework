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
 * @package    Zend_Cloud
 * @subpackage DocumentService
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\DocumentService;

use Zend\Cloud\DocumentService\Adapter,
    Zend\Cloud\DocumentService\Document,
    Zend\Cloud\DocumentService\Factory,
    Zend\Cloud\DocumentService\QueryAdapter,
    PHPUnit_Framework_TestCase as PHPUnitTestCase;

/**
 * This class forces the adapter tests to implement tests for all methods on
 * Zend\Cloud\DocumentService.
 *
 * @category   Zend
 * @package    Zend_Cloud_DocumentService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Reference to Document adapter to test
     *
     * @var \Zend\Cloud\DocumentService
     */
    protected $_commonDocument;

    protected $_dummyCollectionNamePrefix = 'TestCollection';

    protected $_dummyDataPrefix = 'TestData';

    protected $_clientType = 'stdClass';

    const ID_FIELD = "__id";

    /**
     * Config object
     *
     * @var \Zend\Config\Config
     */

    protected $_config;

    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 1;

    public function testDocumentService()
    {
        $this->assertTrue($this->_commonDocument instanceof \Zend\Cloud\DocumentService\Adapter\AdapterInterface);
    }

    public function testGetClient()
    {
    	$this->assertTrue(is_a($this->_commonDocument->getClient(), $this->_clientType));
    }

    public function testCreateCollection()
    {
        $name = $this->_collectionName("testCreate");
        $this->_commonDocument->deleteCollection($name);
        $this->_wait();

        $this->_commonDocument->createCollection($name);
        $this->_wait();

        $collections = $this->_commonDocument->listCollections();
        $this->assertContains($name, $collections, "New collection not in the list");
        $this->_wait();

        $this->_commonDocument->deleteCollection($name);
    }

    public function testDeleteCollection()
    {
        $name = $this->_collectionName("testDC");
        $this->_commonDocument->createCollection($name);
        $this->_wait();

        $collections = $this->_commonDocument->listCollections();
        $this->assertContains($name, $collections, "New collection not in the list");
        $this->_wait();

        $this->_commonDocument->deleteCollection($name);
        $this->_wait();
        $this->_wait();

        $collections = $this->_commonDocument->listCollections();
        $this->assertNotContains($name, $collections, "New collection not in the list");
    }

    public function testListCollections()
    {
        $this->_commonDocument->createCollection($this->_collectionName("test3"));
        $this->_commonDocument->createCollection($this->_collectionName("test4"));
        $this->_wait();

        $collections = $this->_commonDocument->listCollections();
        $this->assertContains($this->_collectionName("test3"), $collections, "New collection test3 not in the list");
        $this->assertContains($this->_collectionName("test4"), $collections, "New collection test4 not in the list");
        $this->_wait();

        $this->_commonDocument->deleteCollection($this->_collectionName("test3"));
        $this->_commonDocument->deleteCollection($this->_collectionName("test4"));
    }

    public function testInsertDocument()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testID");
        $this->_commonDocument->createCollection($name);

        $doc = $this->_makeDocument($data[0]);
        $this->_commonDocument->insertDocument($name, $doc);
        $this->_wait();

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc->getId());
        $this->assertTrue($fetchdoc instanceof \Zend\Cloud\DocumentService\Document, "New document not found");

        $this->assertEquals($doc->name, $fetchdoc->name, "Name field wrong");
        $this->assertEquals($doc->keyword, $fetchdoc->keyword, "Keyword field wrong");

        $this->_commonDocument->deleteCollection($name);
    }

    public function testDeleteDocument()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testDel");
        $this->_commonDocument->createCollection($name);

        $doc1 = $this->_makeDocument($data[0]);
        $this->_commonDocument->insertDocument($name, $doc1);
        $this->_wait();

        $doc2 = $this->_makeDocument($data[1]);
        $this->_commonDocument->insertDocument($name, $doc2);
        $this->_wait();

        $this->_commonDocument->deleteDocument($name, $doc1->getId());
        $this->_wait();

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc1->getId());
        $this->assertFalse($fetchdoc, "Delete failed");

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc2->getId());
        $this->assertTrue($fetchdoc instanceof \Zend\Cloud\DocumentService\Document, "New document not found");
        $this->assertEquals($doc2->name, $fetchdoc->name, "Name field wrong");

        $this->_commonDocument->deleteCollection($name);
    }

    public function testReplaceDocument()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testRD");
        $this->_commonDocument->createCollection($name);

        $doc1 = $this->_makeDocument($data[0]);
        $this->_commonDocument->insertDocument($name, $doc1);
        $doc2 = $this->_makeDocument($data[1]);
        $this->_commonDocument->insertDocument($name, $doc2);
        $this->_wait();

        $doc3 = $this->_makeDocument($data[2]);
        $newdoc = new Document($doc3->getFields(), $doc1->getId());
        $this->_commonDocument->replaceDocument($name, $newdoc);

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc1->getId());
        $this->assertTrue($fetchdoc instanceof \Zend\Cloud\DocumentService\Document, "New document not found");
        $this->assertEquals($doc3->name, $fetchdoc->name, "Name field did not update");
        $this->assertEquals($doc3->keyword, $fetchdoc->keyword, "Keywords did not update");

        $this->_commonDocument->deleteCollection($name);
    }

    public function testUpdateDocumentIDFields()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testUD1");
        $this->_commonDocument->createCollection($name);

        $doc = $this->_makeDocument($data[0]);
        $this->_commonDocument->insertDocument($name, $doc);
        $this->_wait();
        $doc1 = $this->_makeDocument($data[1]);
        $this->_commonDocument->updateDocument($name, $doc->getId(), $doc1->getFields());
        $this->_wait();

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc->getId());
        $this->assertTrue($fetchdoc instanceof \Zend\Cloud\DocumentService\Document, "New document not found");
        $this->assertEquals($doc1->name, $fetchdoc->name, "Name field did not update");

         $this->_commonDocument->deleteCollection($name);
    }

    public function testUpdateDocumentIDDoc()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testUD2");
        $this->_commonDocument->createCollection($name);
        // id is specified, fields from another doc
        $doc1 = $this->_makeDocument($data[1]);
        $this->_commonDocument->insertDocument($name, $doc1);
        $doc2 = $this->_makeDocument($data[2]);
        $this->_commonDocument->updateDocument($name, $doc1->getId(), $doc2);
        $this->_wait();

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc1->getId());
        $this->assertTrue($fetchdoc instanceof \Zend\Cloud\DocumentService\Document, "New document not found");
        $this->assertEquals($doc2->name, $fetchdoc->name, "Name field did not update");
        $this->assertEquals($doc2->keyword, $fetchdoc->keyword, "Keywords did not update");

         $this->_commonDocument->deleteCollection($name);
    }

    public function testUpdateDocumentDoc()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testUD3");
        $this->_commonDocument->createCollection($name);
        // id is not specified
        $doc2 = $this->_makeDocument($data[2]);
        $doc3 = new Document($this->_makeDocument($data[3])->getFields(), $doc2->getId());
        $this->_commonDocument->insertDocument($name, $doc2);
        $this->_wait();
        $this->_commonDocument->updateDocument($name, null, $doc3);
        $this->_wait();

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc2->getId());
        $this->assertTrue($fetchdoc instanceof \Zend\Cloud\DocumentService\Document, "New document not found");
        $this->assertEquals($doc3->name, $fetchdoc->name, "Name field did not update");
        $this->assertEquals($doc3->keyword, $fetchdoc->keyword, "Keywords did not update");

        $this->_commonDocument->deleteCollection($name);
    }

    public function testQueryString()
    {
        $name = $this->_collectionName("testQuery");
        $doc = $this->_loadData($name);

        $query = $this->_queryString($name, $doc[1]->getId(), $doc[2]->getId());
        $fetchdocs = $this->_commonDocument->query($name, $query);

        $this->assertTrue(count($fetchdocs) >= 2, "Query failed to fetch 2 fields");
        foreach($fetchdocs as $fdoc) {
            $this->assertContains($fdoc["name"], array($doc[1]->name, $doc[2]->name), "Wrong name in results");
            $this->assertContains($fdoc["author"], array($doc[1]->author, $doc[2]->author), "Wrong name in results");
        }

        $this->_commonDocument->deleteCollection($name);
    }

    public function testQueryStruct()
    {
        $name = $this->_collectionName("testStructQuery1");
        $doc = $this->_loadData($name);

        // query by ID
        $query = $this->_commonDocument->select();
        $this->assertTrue($query instanceof \Zend\Cloud\DocumentService\QueryAdapter);
        $query->from($name)->whereId($doc[1]->getId());
        $fetchdocs = $this->_commonDocument->query($name, $query);
        $this->assertEquals(1, count($fetchdocs), 'Query: ' . $query->assemble() . "\nDocuments:\n" . var_export($fetchdocs, 1));
        foreach ($fetchdocs as $fdoc) {
            $this->assertEquals($doc[1]->name, $fdoc["name"], "Wrong name in results");
            $this->assertEquals($doc[1]->author, $fdoc["author"], "Wrong author in results");
        }

        $this->_commonDocument->deleteCollection($name);
    }

    public function testQueryStructWhere()
    {
        $name = $this->_collectionName("testStructQuery2");
        $doc = $this->_loadData($name);

        // query by field condition
        $query = $this->_commonDocument->select()
            ->from($name)->where("year > ?", array(1945));
        $fetchdocs = $this->_commonDocument->query($name, $query);
        $this->assertEquals(3, count($fetchdocs));
        foreach($fetchdocs as $fdoc) {
            $this->assertTrue($fdoc["year"] > 1945);
        }

        $this->_commonDocument->deleteCollection($name);
    }

    public function testQueryStructLimit()
    {
        $name = $this->_collectionName("testStructQuery3");
        $doc = $this->_loadData($name);

        // query with limit
        $query = $this->_commonDocument->select()
            ->from($name)->where("year > ?", array(1945))->limit(1);
        $fetchdocs = $this->_commonDocument->query($name, $query);
        $this->assertEquals(1, count($fetchdocs));
        foreach($fetchdocs as $fdoc) {
            $this->assertTrue($fdoc["year"] > 1945);
            $this->assertContains($fdoc["name"], array($doc[0]->name, $doc[2]->name, $doc[3]->name), "Wrong name in results");
        }

        $this->_commonDocument->deleteCollection($name);
    }

    public function testQueryStructOrder()
    {
        $name = $this->_collectionName("testStructQuery4");
        $doc = $this->_loadData($name);

        // query with sort
        $query = $this->_commonDocument->select()
            ->from($name)->where("year > ?", array(1945))->order("year", "desc");
        $fetchdocs = $this->_commonDocument->query($name, $query);
        $this->assertEquals(3, count($fetchdocs));
        foreach ($fetchdocs as $fdoc) {
            $this->assertEquals($doc[2]->name, $fdoc["name"]);
            break;
        }

        $this->_commonDocument->deleteCollection($name);
    }

    public function setUp()
    {
        $this->_config = $this->_getConfig();
        $this->_commonDocument = \Zend\Cloud\DocumentService\Factory::getAdapter($this->_config);
        parent::setUp();
    }

    abstract protected function _getConfig();
    abstract protected function _getDocumentData();
    abstract protected function _queryString($domain, $s1, $s2);

    protected function _collectionName($name)
    {
        return $this->_dummyCollectionNamePrefix . $name; //.mt_rand();
    }

    protected function _wait() {
        sleep($this->_waitPeriod);
    }

    protected function _makeDocument($arr)
    {
        $id = $arr[self::ID_FIELD];
        unset($arr[self::ID_FIELD]);
        return new \Zend\Cloud\DocumentService\Document($arr, $id);
    }

    protected function _loadData($name)
    {
        $data = $this->_getDocumentData();
        $this->_commonDocument->createCollection($name);
        for($i=0; $i<count($data); $i++) {
            $doc[$i] = $this->_makeDocument($data[$i]);
            $this->_commonDocument->insertDocument($name, $doc[$i]);
        }
        $this->_wait();
        return $doc;
    }
}
