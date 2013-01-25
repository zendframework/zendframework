<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Writer;

use Zend\Log\Writer\ZendMonitor;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class ZendMonitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group ZF-10081
     */
    public function testWrite()
    {
        $writer = new ZendMonitor();
        $writer->write(array(
            'message' => 'my mess',
            'priority' => 1
        ));
    }

    public function testIsEnabled()
    {
        $writer = new ZendMonitor();
        $this->assertInternalType('boolean', $writer->isEnabled());
    }
}
