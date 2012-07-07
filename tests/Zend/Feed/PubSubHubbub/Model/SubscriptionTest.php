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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Feed\PubSubHubbub\Model;

use DateTime;
use PDO;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Feed\PubSubHubbub\Model\Subscription;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Pubsubhubbub_Model
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SubscriptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group ZF-10069
     */
    public function testAllOperations()
    {
        $adapter = $this->initDb();
        $table = new TableGateway('subscription', $adapter);
        
        $subscription = new Subscription($table);
        
        $id = uniqid();
        $this->assertFalse($subscription->hasSubscription($id));
        $this->assertFalse($subscription->getSubscription($id));
        $this->assertFalse($subscription->deleteSubscription($id));
        $this->assertTrue($subscription->setSubscription(array('id' => $id)));

        $this->assertTrue($subscription->hasSubscription($id));
        $dataSubscription = $subscription->getSubscription($id);
        $this->assertInternalType('array', $dataSubscription);
        $keys = array('id', 'topic_url', 'hub_url',
                      'created_time', 'lease_seconds',
                      'verify_token', 'secret',
                      'expiration_time', 'subscription_state');

        $this->assertSame($keys, array_keys($dataSubscription));
        $this->assertFalse($subscription->setSubscription(array('id' => $id)));
        $this->assertTrue($subscription->deleteSubscription($id));
    }

    public function testImpemetsSubscriptionInterface()
    {
        $reflection = new \ReflectionClass('Zend\Feed\PubSubHubbub\Model\Subscription');
        $this->assertTrue($reflection->implementsInterface('Zend\Feed\PubSubHubbub\Model\SubscriptionPersistenceInterface'));
        unset($reflection);
    }

    public function testCurrentTimeSetterAndGetter()
    {
        $now = new DateTime();
        $subscription = new Subscription(new TableGateway('subscription', $this->initDb()));
        $subscription->setNow($now);
        $this->assertSame($subscription->getNow(), $now);
    }

    protected function initDb()
    {
        if (!extension_loaded('pdo')
            || !in_array('sqlite', PDO::getAvailableDrivers())
        ) {
            $this->markTestSkipped('Test only with pdo_sqlite');
        }
        $db = new DbAdapter(array('driver' => 'pdo_sqlite', 'dsn' => 'sqlite::memory:'));
        $this->createTable($db);
        
        return $db;
    }

    protected function createTable(DbAdapter $db)
    {
        $sql = "CREATE TABLE subscription ("
             .      "id varchar(32) PRIMARY KEY NOT NULL DEFAULT '', "
             .      "topic_url varchar(255) DEFAULT NULL, "
             .      "hub_url varchar(255) DEFAULT NULL, "
             .      "created_time datetime DEFAULT NULL, "
             .      "lease_seconds bigint(20) DEFAULT NULL, "
             .      "verify_token varchar(255) DEFAULT NULL, "
             .      "secret varchar(255) DEFAULT NULL, "
             .      "expiration_time datetime DEFAULT NULL, "
             .      "subscription_state varchar(12) DEFAULT NULL"
             . ");";

        $db->query($sql)->execute();
    }
}
