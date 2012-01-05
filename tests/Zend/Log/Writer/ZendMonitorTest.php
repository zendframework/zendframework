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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Writer;

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log_Writer_ZendMonitor */
require_once 'Zend/Log/Writer/ZendMonitor.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class ZendMonitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group ZF-10081
     */
    public function testWrite()
    {
        $writer = new \Zend\Log\Writer\ZendMonitor();
        $writer->write(array('message' => 'my mess', 'priority' => 1));
    }

    public function testFactory()
    {
        $cfg = array();

        $writer = \Zend\Log\Writer\ZendMonitor::factory($cfg);
        $this->assertTrue($writer instanceof \Zend\Log\Writer\ZendMonitor);
    }

    public function testIsEnabled()
    {
        $writer = new \Zend\Log\Writer\ZendMonitor();
        $this->assertInternalType('boolean', $writer->isEnabled());
    }
}
