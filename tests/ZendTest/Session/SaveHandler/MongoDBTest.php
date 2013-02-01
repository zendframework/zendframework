<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session\SaveHandler;

use Mongo;
use Zend\Session\SaveHandler\MongoDB;
use Zend\Session\SaveHandler\MongoDBOptions;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @group      Zend_Session
 */
class MongoDBTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mongo|MongoClient
     */
    protected $mongo;

    /**
     * MongoCollection instance
     *
     * @var MongoCollection
     */
    protected $mongoCollection;

    /**
     * @var Zend\Session\SaveHandler\MongoDBOptions
     */
    protected $options;

    /**
     * Setup performed prior to each test method
     *
     * @return void
     */
    public function setUp()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('Zend\Session\SaveHandler\MongoDB tests are not enabled due to missing Mongo extension');
        }

        $this->options = new MongoDBOptions(array(
            'database' => 'zf2_tests',
            'collection' => 'sessions',
        ));

        $mongoClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';

        $this->mongo = new $mongoClass();
        $this->mongoCollection = $this->mongo->selectCollection($this->options->getDatabase(), $this->options->getCollection());
    }

    /**
     * Tear-down operations performed after each test method
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->mongoCollection) {
            $this->mongoCollection->drop();
        }
    }

    public function testReadWrite()
    {
        $saveHandler = new MongoDB($this->mongo, $this->options);
        $this->assertTrue($saveHandler->open('savepath', 'sessionname'));

        $id = '242';
        $data = array('foo' => 'bar', 'bar' => array('foo' => 'bar'));

        $this->assertTrue($saveHandler->write($id, serialize($data)));
        $this->assertEquals($data, unserialize($saveHandler->read($id)));

        $data = array('foo' => array(1, 2, 3));

        $this->assertTrue($saveHandler->write($id, serialize($data)));
        $this->assertEquals($data, unserialize($saveHandler->read($id)));
    }

    public function testReadDestroysExpiredSession()
    {
        /* Note: due to the session save handler's open() method reading the
         * "session.gc_maxlifetime" INI value directly, it's necessary to set
         * that to simulate natural session expiration.
         */
        $oldMaxlifetime = ini_get('session.gc_maxlifetime');
        ini_set('session.gc_maxlifetime', 0);

        $saveHandler = new MongoDB($this->mongo, $this->options);
        $this->assertTrue($saveHandler->open('savepath', 'sessionname'));

        $id = '242';
        $data = array('foo' => 'bar');

        $this->assertNull($this->mongoCollection->findOne(array('_id' => $id)));

        $this->assertTrue($saveHandler->write($id, serialize($data)));
        $this->assertNotNull($this->mongoCollection->findOne(array('_id' => $id)));
        $this->assertEquals('', $saveHandler->read($id));
        $this->assertNull($this->mongoCollection->findOne(array('_id' => $id)));

        ini_set('session.gc_maxlifetime', $oldMaxlifetime);
    }

    public function testGarbageCollection()
    {
        $saveHandler = new MongoDB($this->mongo, $this->options);
        $this->assertTrue($saveHandler->open('savepath', 'sessionname'));

        $data = array('foo' => 'bar');

        $this->assertTrue($saveHandler->write(123, serialize($data)));
        $this->assertTrue($saveHandler->write(456, serialize($data)));
        $this->assertEquals(2, $this->mongoCollection->count());
        $saveHandler->gc(5);
        $this->assertEquals(2, $this->mongoCollection->count());

        /* Note: MongoDate uses micro-second precision, so even a maximum
         * lifetime of zero would not match records that were just inserted.
         * Use a negative number instead.
         */
        $saveHandler->gc(-1);
        $this->assertEquals(0, $this->mongoCollection->count());
    }

    /**
     * @expectedException MongoCursorException
     */
    public function testWriteExceptionEdgeCaseForChangedSessionName()
    {
        $saveHandler = new MongoDB($this->mongo, $this->options);
        $this->assertTrue($saveHandler->open('savepath', 'sessionname'));

        $id = '242';
        $data = array('foo' => 'bar');

        /* Note: a MongoCursorException will be thrown if a record with this ID
         * already exists with a different session name, since the upsert query
         * cannot insert a new document with the same ID and new session name.
         * This should only happen if ID's are not unique or if the session name
         * is altered mid-process.
         */
        $saveHandler->write($id, serialize($data));
        $saveHandler->open('savepath', 'sessionname_changed');
        $saveHandler->write($id, serialize($data));
    }
}
