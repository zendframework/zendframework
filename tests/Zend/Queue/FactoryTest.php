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
 * @package    Zend_Queue
 * @subpackage UnitTests
 */

namespace ZendTest\Queue;
use Zend\Queue;

/** PHPUnit Test Case */

/** Zend_Queue */

/** Zend_Queue_Exception */

/** Zend_Queue_Adapter_* */


/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @group      Zend_Queue
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    protected function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->_originaltimezone);
    }

    public function testDb()
    {
        if ( TESTS_ZEND_QUEUE_DB === false ) {
            $this->markTestSkipped('DB setup required');
        }

        $options = json_decode(TESTS_ZEND_QUEUE_DB, true);

        $config = array('name'          => 'queue1',
                        'driverOptions' => array('host'     => $options['host'],
                                                 'username' => $options['username'],
                                                 'password' => $options['password'],
                                                 'dbname'   => $options['dbname'],
                                                 'type'     => $options['type'],
                                                 'port'     => $options['port'])); // optional parameter

        $adapter = new Queue\Queue('DB', $config);

        $this->assertTrue($adapter instanceof Queue\Queue);
    }

    public function testMemcacheq()
    {
        if ( TESTS_ZEND_QUEUE_MEMCACHEQ_HOST === false ||
             TESTS_ZEND_QUEUE_MEMCACHEQ_PORT === false ) {
            $this->markTestSkipped('MemcacheQ setup required');
        }

        $config = array('name'          => 'queue1',
                        'driverOptions' => array('host' => TESTS_ZEND_QUEUE_MEMCACHEQ_HOST,
                                                 'port' => TESTS_ZEND_QUEUE_MEMCACHEQ_PORT));

        $adapter = new Queue\Queue('Memcacheq', $config);

        $this->assertTrue($adapter instanceof Queue\Queue);
    }

    public function testActivemq()
    {
        if ( TESTS_ZEND_QUEUE_ACTIVEMQ_SCHEME === false ||
             TESTS_ZEND_QUEUE_ACTIVEMQ_HOST === false ||
             TESTS_ZEND_QUEUE_ACTIVEMQ_PORT === false ) {
            $this->markTestSkipped('ActiveMQ setup required');
        }

        $config = array('name'          => 'queue1',
                        'driverOptions' => array('host'     => TESTS_ZEND_QUEUE_ACTIVEMQ_HOST,
                                                 'port'     => TESTS_ZEND_QUEUE_ACTIVEMQ_PORT,
                                                 'scheme'   => TESTS_ZEND_QUEUE_ACTIVEMQ_SCHEME,
                                                 'username' => '',
                                                 'password' => ''));

        $adapter = new Queue\Queue('Activemq', $config);

        $this->assertTrue($adapter instanceof Queue\Queue);
    }

    public function testArray()
    {
        $config = array('name'          => 'queue1',
                        'driverOptions' => array());

        $adapter = new Queue\Queue('ArrayAdapter', $config);

        $this->assertTrue($adapter instanceof Queue\Queue);
    }
}
