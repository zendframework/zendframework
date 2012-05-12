<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace ZendTest\Service\WindowsAzure;

use Zend\Service\WindowsAzure\RetryPolicy\AbstractRetryPolicy;
use Zend\Service\WindowsAzure\Storage\DynamicTableEntity;
use Zend\Service\WindowsAzure\Storage\Table;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 */
class TableStorageTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        if (!TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $this->markTestSkipped('Windows Azure Tests disabled');
        }
    }

    /**
     * Test teardown
     */
    protected function tearDown()
    {
        if (!TESTS_ZEND_SERVICE_WINDOWSAZURE_BLOB_RUNTESTS) {
            return;
        }
        $storageClient = $this->createStorageInstance();
        for ($i = 1; $i <= self::$uniqId; $i++) {
            try { $storageClient->deleteTable(TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_TABLENAME_PREFIX . $i); } catch (\Exception $e) { }
        }
    }

    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNONPROD) {
            $storageClient = new Table(TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, AbstractRetryPolicy::retryN(10, 250));
        } else {
            $storageClient = new Table(TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, AbstractRetryPolicy::retryN(10, 250));
        }

        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY) {
            $storageClient->setProxy(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_PORT, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_CREDENTIALS);
        }

        return $storageClient;
    }

    protected static $uniqId = 0;

    protected function generateName()
    {
        self::$uniqId++;
        return TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_TABLENAME_PREFIX . self::$uniqId;
    }

    /**
     * Test create table
     */
    public function testCreateTable()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();

        $result = $storageClient->createTable($tableName);
        $this->assertEquals($tableName, $result->Name);

        $result = $storageClient->listTables();
        $this->assertEquals(1, count($result));
        $this->assertEquals($tableName, $result[0]->Name);
    }

    /**
     * Test table exists
     */
    public function testTableExists()
    {
        $tableName1 = $this->generateName();
        $tableName2 = $this->generateName();
        $storageClient = $this->createStorageInstance();

        $storageClient->createTable($tableName1);
        $storageClient->createTable($tableName2);

        $result = $storageClient->tableExists($tableName2);
        $this->assertTrue($result);

        $result = $storageClient->tableExists(md5(time()));
        $this->assertFalse($result);
    }

    /**
     * Test list tables
     */
    public function testListTables()
    {
        $tableName1 = $this->generateName();
        $tableName2 = $this->generateName();
        $storageClient = $this->createStorageInstance();

        $storageClient->createTable($tableName1);
        $storageClient->createTable($tableName2);

        $result = $storageClient->listTables();
        $this->assertEquals(2, count($result));
        $this->assertEquals($tableName1, $result[0]->Name);
        $this->assertEquals($tableName2, $result[1]->Name);
    }

    /**
     * Test delete table
     */
    public function testDeleteTable()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();

        $storageClient->createTable($tableName);
        $storageClient->deleteTable($tableName);

        $result = $storageClient->listTables();
        $this->assertEquals(0, count($result));
    }

    /**
     * Test insert entity
     */
    public function testInsertEntity()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];

        $result = $storageClient->insertEntity($tableName, $entity);

        $this->assertNotEquals('0001-01-01T00:00:00', $result->getTimestamp());
        $this->assertNotEquals('', $result->getEtag());
        $this->assertEquals($entity, $result);
    }

    /**
     * Test delete entity, not taking etag into account
     */
    public function testDeleteEntity_NoEtag()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];
        $result = $storageClient->insertEntity($tableName, $entity);

        $this->assertEquals($entity, $result);

        $storageClient->deleteEntity($tableName, $entity);
    }

    /**
     * Test delete entity, taking etag into account
     */
    public function testDeleteEntity_Etag()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];

        $result = $storageClient->insertEntity($tableName, $entity);

        $this->assertEquals($entity, $result);

        // Set "old" etag
        $entity->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');

        $exceptionThrown = false;
        try {
            $storageClient->deleteEntity($tableName, $entity, true);
        } catch (\Exception $ex) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown);
    }

    /**
     * Test retrieve entity by id
     */
    public function testRetrieveEntityById()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];

        $storageClient->insertEntity($tableName, $entity);

        $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey(), 'TSTest_TestEntity');
        $this->assertEquals($entity, $result);
    }

    /**
     * Test retrieve entity by id (> 256 key characters)
     */
    public function testRetrieveEntityById_Large()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];
        $entity->setPartitionKey(str_repeat('a', 200));
        $entity->setRowKey(str_repeat('a', 200));

        $storageClient->insertEntity($tableName, $entity);

        $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey(), 'TSTest_TestEntity');
        $this->assertEquals($entity, $result);
    }

    /**
     * Test retrieve entity by id, DynamicTableEntity
     */
    public function testRetrieveEntityById_DynamicTableEntity()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];

        $storageClient->insertEntity($tableName, $entity);

        $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey());
        $this->assertEquals($entity->FullName, $result->Name);
        $this->assertInstanceOf('Zend_Service_WindowsAzure_Storage_DynamicTableEntity', $result);
    }

    /**
     * Test update entity, not taking etag into account
     */
    public function testUpdateEntity_NoEtag()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];

        $storageClient->insertEntity($tableName, $entity);
        $entity->Age = 0;

        $result = $storageClient->updateEntity($tableName, $entity);

        $this->assertNotEquals('0001-01-01T00:00:00', $result->getTimestamp());
        $this->assertNotEquals('', $result->getEtag());
        $this->assertEquals(0, $result->Age);
        $this->assertEquals($entity, $result);
    }

    /**
     * Test update entity, taking etag into account
     */
    public function testUpdateEntity_Etag()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];

        $storageClient->insertEntity($tableName, $entity);
        $entity->Age = 0;

        // Set "old" etag
        $entity->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');

        $exceptionThrown = false;
        try {
            $storageClient->updateEntity($tableName, $entity, true);
        } catch (\Exception $ex) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown);
    }

    /**
     * Test merge entity, not taking etag into account
     */
    public function testMergeEntity_NoEtag()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];

        $storageClient->insertEntity($tableName, $entity);

        $dynamicEntity = new DynamicTableEntity($entity->getPartitionKey(), $entity->getRowKey());
        $dynamicEntity->Myproperty = 10;
        $dynamicEntity->Otherproperty = "Test";
        $dynamicEntity->Age = 0;

        $storageClient->mergeEntity($tableName, $dynamicEntity, false, array('Myproperty', 'Otherproperty')); // only update 'Myproperty' and 'Otherproperty'

        $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey());

        $this->assertNotEquals('0001-01-01T00:00:00', $result->getTimestamp());
        $this->assertNotEquals('', $result->getEtag());
        $this->assertNotEquals(0, $result->Age);
        $this->assertEquals($entity->FullName, $result->Name);
        $this->assertEquals($dynamicEntity->Myproperty, $result->Myproperty);
        $this->assertEquals($dynamicEntity->Otherproperty, $result->Otherproperty);
    }

    /**
     * Test merge entity, taking etag into account
     */
    public function testMergeEntity_Etag()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(1);
        $entity = $entities[0];

        $storageClient->insertEntity($tableName, $entity);

        $dynamicEntity = new DynamicTableEntity($entity->getPartitionKey(), $entity->getRowKey());
        $dynamicEntity->Myproperty = 10;
        $dynamicEntity->Otherproperty = "Test";
        $dynamicEntity->Age = 0;

        // Set "old" etag
        $entity->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');

        $exceptionThrown = false;
        try {
            $storageClient->mergeEntity($tableName, $dynamicEntity, true);
        } catch (\Exception $ex) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown);
    }

    /**
     * Test retrieve entities, all
     */
    public function testRetrieveEntities_All()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(20);
        foreach ($entities as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        $result = $storageClient->retrieveEntities($tableName, 'TSTest_TestEntity');
        $this->assertEquals(20, count($result));
    }

    /**
     * Test retrieve entities, all, DynamicTableEntity
     */
    public function testRetrieveEntities_All_DynamicTableEntity()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(20);
        foreach ($entities as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        $result = $storageClient->retrieveEntities($tableName);
        $this->assertEquals(20, count($result));

        foreach ($result as $item) {
            $this->assertInstanceOf('Zend_Service_WindowsAzure_Storage_DynamicTableEntity', $item);
        }
    }

    /**
     * Test retrieve entities, filtered
     */
    public function testRetrieveEntities_Filtered()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(5);
        foreach ($entities as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        $result = $storageClient->retrieveEntities($tableName, 'PartitionKey eq \'' . $entities[0]->getPartitionKey() . '\' and RowKey eq \'' . $entities[0]->getRowKey() . '\'', 'TSTest_TestEntity');
        $this->assertEquals(1, count($result));
    }

    /**
     * Test retrieve entities, fluent interface
     */
    public function testRetrieveEntities_Fluent1()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(10);
        foreach ($entities as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        $result = $storageClient->retrieveEntities(
            $storageClient->select()
                          ->from($tableName)
                          ->where('Name eq ?', $entities[0]->FullName)
                          ->andWhere('RowKey eq ?', $entities[0]->getRowKey()),
            'TSTest_TestEntity'
        );

        $this->assertEquals(1, count($result));
        $this->assertEquals($entities[0], $result[0]);
    }

    /**
     * Test retrieve entities, fluent interface
     */
    public function testRetrieveEntities_Fluent2()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(10);
        foreach ($entities as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        $result = $storageClient->retrieveEntities(
            $storageClient->select()
                          ->from($tableName)
                          ->where('Name eq ?', $entities[0]->FullName)
                          ->andWhere('PartitionKey eq ?', $entities[0]->getPartitionKey()),
            'TSTest_TestEntity'
        );

        $this->assertEquals(1, count($result));
        $this->assertEquals($entities[0], $result[0]);
    }

    /**
     * Test retrieve entities, fluent interface, top specification
     */
    public function testRetrieveEntities_Fluent_Top()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(10);
        foreach ($entities as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        $result = $storageClient->retrieveEntities(
            $storageClient->select()->top(4)
                          ->from($tableName),
            'TSTest_TestEntity'
        );

        $this->assertEquals(4, count($result));
    }

    /**
     * Test batch commit, success
     */
    public function testBatchCommit_Success()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(20);
        $entities1 = array_slice($entities, 0, 10);
        $entities2 = array_slice($entities, 10, 10);

        // Insert entities
        foreach ($entities1 as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        // Start batch
        $batch = $storageClient->startBatch();
        $this->assertInstanceOf('Zend_Service_WindowsAzure_Storage_Batch', $batch);

        // Insert entities in batch
        foreach ($entities2 as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        // Delete entities
        foreach ($entities1 as $entity) {
            $storageClient->deleteEntity($tableName, $entity);
        }

        // Commit
        $batch->commit();

        // Verify
        $result = $storageClient->retrieveEntities($tableName);
        $this->assertEquals(10, count($result));
    }

    /**
     * Test batch rollback, success
     */
    public function testBatchRollback_Success()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(10);

        // Start batch
        $batch = $storageClient->startBatch();
        $this->assertInstanceOf('Zend_Service_WindowsAzure_Storage_Batch', $batch);

        // Insert entities in batch
        foreach ($entities as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        // Rollback
        $batch->rollback();

        // Verify
        $result = $storageClient->retrieveEntities($tableName);
        $this->assertEquals(0, count($result));
    }

    /**
     * Test batch commit, fail updates
     */
    public function testBatchCommit_FailUpdates()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(10);
        foreach ($entities as $entity) {
            $storageClient->insertEntity($tableName, $entity);
        }

        // Make some entity updates with "old" etags
        $entities[0]->Age = 0;
        $entities[0]->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');
        $entities[1]->Age = 0;
        $entities[1]->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');
        $entities[2]->Age = 0;

        // Start batch
        $batch = $storageClient->startBatch();
        $this->assertInstanceOf('Zend_Service_WindowsAzure_Storage_Batch', $batch);

        // Update entities in batch
        $storageClient->updateEntity($tableName, $entities[0], true);
        $storageClient->updateEntity($tableName, $entities[1], true);
        $storageClient->updateEntity($tableName, $entities[2], true);

        // Commit
        $exceptionThrown = false;
        try {
            $batch->commit();
        } catch (\Exception $ex) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown);
    }

    /**
     * Test batch commit, fail partition
     */
    public function testBatchCommit_FailPartition()
    {
        $tableName = $this->generateName();
        $storageClient = $this->createStorageInstance();
        $storageClient->createTable($tableName);

        $entities = $this->_generateEntities(10);

        // Start batch
        $batch = $storageClient->startBatch();
        $this->assertInstanceOf('Zend_Service_WindowsAzure_Storage_Batch', $batch);

        // Insert entities in batch
        foreach ($entities as $entity) {
            $entity->setPartitionKey('partition' . rand(1, 9));
            $storageClient->insertEntity($tableName, $entity);
        }

        // Commit
        $exceptionThrown = false;
        try {
            $batch->commit();
        } catch (\Exception $ex) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown);

        // Verify
        $result = $storageClient->retrieveEntities($tableName);
        $this->assertEquals(0, count($result));
    }

    /**
     * Generate entities
     *
     * @param int 		$amount Number of entities to generate
     * @return array 			Array of TSTest_TestEntity
     */
    protected function _generateEntities($amount = 1)
    {
        $returnValue = array();

        for ($i = 0; $i < $amount; $i++) {
            $entity = new TestAsset\Entity('partition1', 'row' . ($i + 1));
            $entity->FullName = md5(uniqid(rand(), true));
            $entity->Age      = rand(1, 130);
            $entity->Visible  = rand(1,2) == 1;

            $returnValue[] = $entity;
        }

        return $returnValue;
    }
}
