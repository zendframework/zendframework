<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendTest\Cloud\QueueService\Adapter;

use ZendTest\Cloud\QueueService\TestCase;
use Zend\Cloud\QueueService\Adapter\ZendQueue;
use Zend\Config\Config;
use Zend\Cloud\QueueService\Factory;

/**
 * @category   Zend
 * @package    ZendTest_Cloud_Queue_Adapter
 * @subpackage UnitTests
 */
class ZendQueueTest extends TestCase
{
    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 0;

    protected $_clientType = 'Zend\Queue\Queue';

    public function testPeekMessages()
    {
        $this->markTestSkipped('ZendQueue does not currently support peeking messages');
    }

    protected function _getConfig()
    {
        $config = new Config(array(
            Factory::QUEUE_ADAPTER_KEY => 'Zend\Cloud\QueueService\Adapter\ZendQueue',
            ZendQueue::ADAPTER         => 'ArrayAdapter'
        ));

        return $config;
    }

}
