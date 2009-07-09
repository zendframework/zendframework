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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Queue */
require_once 'Zend/Queue.php';

/** Zend_Queue_Exception */
require_once 'Zend/Queue/Exception.php';

/** Zend_Queue_Adapter_* */
require_once 'Zend/Queue/Adapter/Array.php';
require_once 'Zend/Queue/Adapter/Db.php';
require_once 'Zend/Queue/Adapter/Memcacheq.php';
require_once 'Zend/Queue/Adapter/Apachemq.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */
class Zend_Queue_FactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        date_default_timezone_set('GMT');
    }

    public function testDb()
    {
        $this->markTestSkipped('Db setup required');

        $config = array('name'           => 'queue1',
                        'driverOptions' => array('host'     => 'db1.domain.tld',
                                                 'username' => 'my_username',
                                                 'password' => 'my_password',
                                                 'dbname'   => 'messaging',
                                                 'type'     => 'pdo_mysql',
                                                 'port'     => 3306)); // optional parameter

        $adapter = new Zend_Queue('Db', $config);

        $this->assertTrue($adapter instanceof Zend_Queue);
    }

    public function testMemcacheq()
    {
        $this->markTestSkipped('MemcacheQ setup required');

        $config = array('name'           => 'queue1',
                        'driverOptions' => array('host' => 'memcacheq.domain.tld',
                                                 'port' => 22201));

        $adapter = new Zend_Queue('Memcacheq', $config);

        $this->assertTrue($adapter instanceof Zend_Queue);
    }

    public function testApachemq()
    {
        $this->markTestSkipped('Stomp setup required');

        $config = array('name'           => 'queue1',
                        'driverOptions' => array('host'     => 'msg.domain.tld',
                                                 'port'     => 61613,
                                                 'username' => 'username',
                                                 'password' => 'password'));

        $adapter = new Zend_Queue('Stomp', $config);

        $this->assertTrue($adapter instanceof Zend_Queue);
    }

    public function testArray()
    {
        $config = array('name' => 'queue1',
                        'driverOptions' => array());

        $adapter = new Zend_Queue('Array', $config);

        $this->assertTrue($adapter instanceof Zend_Queue);
    }
}
