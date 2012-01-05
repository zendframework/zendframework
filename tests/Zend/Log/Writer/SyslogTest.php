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

use ZendTest\Log\TestAsset\CustomSyslogWriter,
    Zend\Log\Writer\Syslog as SyslogWriter,
    \Zend\Log\Logger,
    \Zend\Log\Formatter\Simple as SimpleFormatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class SyslogTest extends \PHPUnit_Framework_TestCase
{
    public function testWrite()
    {
        $fields = array('message' => 'foo', 'priority' => LOG_NOTICE);
        $writer = new SyslogWriter();
        $writer->write($fields);
    }

    public function testFactory()
    {
        $cfg = array(
            'application' => 'my app',
            'facility'    => LOG_USER
        );

        $writer = SyslogWriter::factory($cfg);
        $this->assertTrue($writer instanceof SyslogWriter);
    }

    /**
     * @group ZF-7603
     */
    public function testThrowExceptionValueNotPresentInFacilities()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Invalid log facility provided');
        $writer = new SyslogWriter();
        $writer->setFacility(LOG_USER * 1000);
    }

    /**
     * @group ZF-7603
     */
    public function testThrowExceptionIfFacilityInvalidInWindows()
    {
        if ('WIN' != strtoupper(substr(PHP_OS, 0, 3))) {
            $this->markTestSkipped('Run only in windows');
        }
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Only LOG_USER is a valid');
        $writer = new SyslogWriter();
        $writer->setFacility(LOG_AUTH);
    }

    /**
     * @group ZF-8953
     */
    public function testFluentInterface()
    {
        $writer   = new SyslogWriter();
        $instance = $writer->setFacility(LOG_USER)
                           ->setApplicationName('my_app');

        $this->assertTrue($instance instanceof SyslogWriter);
    }

    /**
     * @group ZF-10769
     */
    public function testPastFacilityViaConstructor()
    {
        $writer = new CustomSyslogWriter(array('facility' => LOG_USER));
        $this->assertEquals(LOG_USER, $writer->getFacility());
    }

    /**
     * @group ZF-8382
     */
    public function testWriteWithFormatter()
    {
        $event = array(
            'message' => 'tottakai',
            'priority' => Logger::ERR
        );

        $writer = SyslogWriter::factory(array());
        $formatter = new SimpleFormatter('%message% (this is a test)');
        $writer->setFormatter($formatter);

        $writer->write($event);
    }
}
