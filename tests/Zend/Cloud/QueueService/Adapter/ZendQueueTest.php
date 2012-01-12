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
 * @package    Zend\Cloud\Queue\Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\QueueService\Adapter;

use ZendTest\Cloud\QueueService\TestCase,
    Zend\Cloud\QueueService\Adapter\ZendQueue,
    Zend\Config\Config,
    Zend\Cloud\QueueService\Factory;

// Call Zend\Cloud\QueueService\Adapter\ZendQueueTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend\Cloud\QueueService\Adapter\ZendQueueTest::main");
}

/**
 * @category   Zend
 * @package    Zend\Cloud\Queue\Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

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

if (PHPUnit_MAIN_METHOD == 'Zend\Cloud\QueueService\Adapter\ZendQueueTest::main') {
    ZendQueueTest::main();
}
