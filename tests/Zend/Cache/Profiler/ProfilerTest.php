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
class ProfilerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The profiler instance
     *
     * @var Zend\Cache\Profiler\Profiler
     */
    protected $_profiler;

    public function setUp()
    {
        $this->_profiler = new Profiler\Profiler();
        $this->_profiler->setEnabled(true);
    }

    public function testAddAndHasProfile()
    {
        $profile = new Profiler\Profile();

        // test missing profile
        $this->assertFalse($this->_profiler->hasProfile($profile));
        $this->assertFalse($this->_profiler->hasProfile($profile->getProfileId()));

        // add profile
        $this->assertSame($this->_profiler, $this->_profiler->addProfile($profile));

        // test existing profile
        $this->assertTrue($this->_profiler->hasProfile($profile));
        $this->assertTrue($this->_profiler->hasProfile($profile->getProfileId()));
    }

    public function testGetProfile()
    {
        $profile = new Profiler\Profile();
        $this->_profiler->addProfile($profile);

        $this->assertSame($profile, $this->_profiler->getProfile($profile));
        $this->assertSame($profile, $this->_profiler->getProfile($profile->getProfileId()));
    }

    public function testGetProfiles()
    {
        $profile1 = new Profiler\Profile();
        $profile2 = new Profiler\Profile();
        $this->_profiler->addProfile($profile1);
        $this->_profiler->addProfile($profile2);

        $this->assertEquals(array(
            $profile1->getProfileId() => $profile1,
            $profile2->getProfileId() => $profile2,
        ), $this->_profiler->getProfiles());
    }

    public function testGetLastProfile()
    {
        $profile1 = new Profiler\Profile();
        $profile2 = new Profiler\Profile();
        $this->_profiler->addProfile($profile1);
        $this->_profiler->addProfile($profile2);

        $this->assertSame($profile2, $this->_profiler->getLastProfile());
    }

    public function testGetMissingProfileThrowsRuntimeException()
    {
        $profile = new Profiler\Profile();

        $this->setExpectedException('Zend\Cache\Exception\RuntimeException');
        $this->_profiler->getProfile($profile);
    }

    public function testRemoveProfile()
    {
        $profile = new Profiler\Profile();

        // test remove profile by instance
        $this->_profiler->addProfile($profile);
        $this->assertSame($this->_profiler, $this->_profiler->removeProfile($profile));
        $this->assertFalse($this->_profiler->hasProfile($profile));

        // test remove profile by id
        $this->_profiler->addProfile($profile);
        $this->assertSame($this->_profiler, $this->_profiler->removeProfile($profile->getProfileId()));
        $this->assertFalse($this->_profiler->hasProfile($profile));
    }

    public function testClearProfiles()
    {
        // add three profiles
        $profile1 = new Profiler\Profile();
        $profile2 = new Profiler\Profile();
        $profile3 = new Profiler\Profile();
        $this->_profiler->addProfile($profile1);
        $this->_profiler->addProfile($profile2);
        $this->_profiler->addProfile($profile3);

        $this->assertSame($this->_profiler, $this->_profiler->clearProfiles());

        $this->assertFalse($this->_profiler->hasProfile($profile1));
        $this->assertFalse($this->_profiler->hasProfile($profile2));
        $this->assertFalse($this->_profiler->hasProfile($profile3));
    }

    public function testCount()
    {
        // test empty set
        $this->assertEquals(0, $this->_profiler->count());

        // add three profiles
        $profile1 = new Profiler\Profile();
        $profile2 = new Profiler\Profile();
        $profile3 = new Profiler\Profile();
        $this->_profiler->addProfile($profile1);
        $this->_profiler->addProfile($profile2);
        $this->_profiler->addProfile($profile3);
        $this->assertEquals(3, $this->_profiler->count());

        // remove an profile
        $this->_profiler->removeProfile($profile2);
        $this->assertEquals(2, $this->_profiler->count());

        // clear all profiles
        $this->_profiler->clearProfiles();
        $this->assertEquals(0, $this->_profiler->count());
    }

    public function testStart()
    {
        $cmd  = 'cmd';
        $args = array('args');

        $profile = $this->_profiler->start($cmd, $args);
        $this->assertInstanceOf('Zend\Cache\Profiler\Profile', $profile);
        $this->assertTrue($profile->hasStarted());
        $this->assertFalse($profile->hasStopped());
        $this->assertEquals($cmd, $profile->getCommand());
        $this->assertEquals($args, $profile->getArguments());
    }

    public function testStartIfDisabled()
    {
        $this->_profiler->setEnabled(false);
        $this->assertFalse($this->_profiler->start('cmd'));
    }

    public function testStop()
    {
        $profile = $this->_profiler->start('cmd');
        $this->_profiler->stop($profile->getProfileId());
        $this->assertTrue($profile->hasStopped());
    }

    public function testTotalElapsedTime()
    {
        // add three profiles
        $profile1 = $this->_profiler->start('cmd1');
        $profile2 = $this->_profiler->start('cmd2');
        $profile3 = $this->_profiler->start('cmd3');
        $profile1->stop();
        $profile2->stop();
        $profile3->stop();

        $expectedElapsedTime = 0;
        foreach ($this->_profiler->getProfiles() as $profile) {
            $expectedElapsedTime+= $profile->getElapsedTime();
        }
        $this->assertEquals($expectedElapsedTime, $this->_profiler->getTotalElapsedTime());
    }

}
