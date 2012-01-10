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
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Service_DeveloperGarden_ConferenceCall
 */

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_DeveloperGarden
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_DeveloperGarden_OfflineConferenceCallTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_ConferenceCall
     */
    protected $_service = null;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $config = array(
            'username' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            'password' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD,
            'environment' => Zend_Service_DeveloperGarden_OfflineConferenceCall_Mock::ENV_MOCK
        );
        $this->service = new Zend_Service_DeveloperGarden_OfflineConferenceCall_Mock($config);
    }

    public function testConferenceDetailObject()
    {
        $o = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $this->assertEquals(
            'My Name',
            $o->getName()
        );

        $this->assertEquals(
            'This is the Conference Description',
            $o->getDescription()
        );

        $this->assertEquals(
            300,
            $o->getDuration()
        );
    }

    public function testConferenceScheduleObject()
    {
        $o = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule(
            0,
            1,
            2,
            3,
            2010,
            4,
            false
        );

        $this->assertEquals(0, $o->getMinute());
        $this->assertEquals(1, $o->getHour());
        $this->assertEquals(2, $o->getDayOfMonth());
        $this->assertEquals(3, $o->getMonth());
        $this->assertEquals(2010,$o->getYear());
        $this->assertEquals(4, $o->getRecurring());
        $this->assertFalse($o->getNotify());
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_ConferenceCall_Exception
     */
    public function testConferenceScheduleObjectException()
    {
        $o = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule(
            0,
            1,
            2,
            3,
            2010,
            4,
            false
        );

        $this->assertEquals(0, $o->getMinute());
        $this->assertEquals(1, $o->getHour());
        $this->assertEquals(2, $o->getDayOfMonth());
        $this->assertEquals(3, $o->getMonth());
        $this->assertEquals(2010,$o->getYear());
        $this->assertEquals(4, $o->getRecurring());
        $this->assertFalse($o->getNotify());
        // should throw an exception
        $this->assertNull($o->setRecurring(99999));
    }

    public function testParticipantDetailObject()
    {
        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $this->assertEquals(
            'Marco',
            $participant->getFirstName()
        );

        $this->assertEquals(
            'Kaiser',
            $participant->getLastName()
        );

        $this->assertEquals(
            '+49 32 0000 0001',
            $participant->getNumber()
        );

        $this->assertEquals(
            'bate@php.net',
            $participant->getEmail()
        );

        $this->assertEquals(1, $participant->getFlags());
        $this->assertTrue((bool)$participant->getFlags());
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testParticipantDetailObjectEmailException()
    {
        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $this->assertEquals(
            'Marco',
            $participant->getFirstName()
        );

        $this->assertEquals(
            'Kaiser',
            $participant->getLastName()
        );

        $this->assertEquals(
            '+49 32 0000 0001',
            $participant->getNumber()
        );

        $this->assertEquals(
            'bate@php.net',
            $participant->getEmail()
        );

        $this->assertEquals(1, $participant->getFlags());
        $this->assertTrue((bool)$participant->getFlags());

        $this->assertNull($participant->setEmail('not-Valid'));
    }

    public function testParticipantStatus()
    {
        $status = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantStatus(
            'muted',
            false
        );

        $this->assertEquals('muted', $status->getName());
        $this->assertFalse($status->getValue());
    }
}

class Zend_Service_DeveloperGarden_OfflineConferenceCall_Mock
    extends Zend_Service_DeveloperGarden_ConferenceCall
{
}
