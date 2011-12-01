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
 * @category   Zend_Cache
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache\Profiles;
use Zend\Cache\Profiler;

/**
 * @category   Zend_Cache
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ProfileTest extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $profile = new Profiler\Profile();

        $this->assertFalse($profile->hasStarted());
        $this->assertNull($profile->getStartTime());

        $this->assertFalse($profile->hasStopped());
        $this->assertNull($profile->getStopTime());
        $this->assertSame(0, $profile->getElapsedTime());
    }

    public function testStart()
    {
        $profile = new Profiler\Profile();

        $startTime = microtime(true);
        $args = array('p1', 'p2');

        $profile->start('command', $args);

        $this->assertTrue($profile->hasStarted());
        $this->assertFalse($profile->hasStopped());

        $this->assertEquals('command', $profile->getCommand());
        $this->assertEquals($args, $profile->getArguments());

        $this->assertGreaterThanOrEqual($startTime, $profile->getStartTime());
        $this->assertLessThan($startTime + 1, $profile->getStartTime());

        $this->assertNull($profile->getStopTime());
        $this->assertGreaterThan(0, $profile->getElapsedTime());
    }

    public function testStartAlreadyStartedThrowsLogicException()
    {
        $profile = new Profiler\Profile();
        $profile->start('cmd');

        $this->setExpectedException('Zend\Cache\Exception\LogicException');
        $profile->start('cmd');
    }

    public function testStop()
    {
        $profile = new Profiler\Profile();

        $profile->start('command');
        usleep(100);
        $profile->stop();

        $this->assertTrue($profile->hasStarted());
        $this->assertTrue($profile->hasStopped());

        $this->assertGreaterThan(0, $profile->getStopTime());
        $this->assertGreaterThan(0, $profile->getElapsedTime());
    }

    public function testStopNotStartedThrowsLogicException()
    {
        $profile = new Profiler\Profile();

        $this->setExpectedException('Zend\Cache\Exception\LogicException');
        $profile->stop();
    }

}
