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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log_Writer_Syslog */
require_once 'Zend/Log/Writer/Syslog.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class Zend_Log_Writer_SyslogTest extends PHPUnit_Framework_TestCase
{
    public function testWrite()
    {
        $fields = array('message' => 'foo', 'priority' => LOG_NOTICE);
        $writer = new Zend_Log_Writer_Syslog();
        $writer->write($fields);
    }

    public function testFactory()
    {
        $cfg = array(
            'application' => 'my app',
            'facility'    => LOG_USER
        );

        $writer = Zend_Log_Writer_Syslog::factory($cfg);
        $this->assertTrue($writer instanceof Zend_Log_Writer_Syslog);
    }

    /**
     * @group ZF-7603
     */
    public function testThrowExceptionValueNotPresentInFacilities()
    {
        try {
            $writer = new Zend_Log_Writer_Syslog();
            $writer->setFacility(LOG_USER * 1000);
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertContains('Invalid log facility provided', $e->getMessage());
        }
    }

    /**
     * @group ZF-7603
     */
    public function testThrowExceptionIfFacilityInvalidInWindows()
    {
        if ('WIN' != strtoupper(substr(PHP_OS, 0, 3))) {
            $this->markTestSkipped('Run only in windows');
        }
        try {
            $writer = new Zend_Log_Writer_Syslog();
            $writer->setFacility(LOG_AUTH);
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertContains('Only LOG_USER is a valid', $e->getMessage());
        }
    }
}
