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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_DeveloperGarden_ConferenceCallTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Service_DeveloperGarden_ConferenceCall
 */
require_once 'Zend/Service/DeveloperGarden/ConferenceCall.php';

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Service_DeveloperGarden_ConferenceCallTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_ConferenceCall
     */
    protected $_service = null;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED') ||
            TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED !== true) {
            $this->markTestSkipped('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED is not enabled');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $config = array(
            'username' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            'password' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD,
            'environment' => Zend_Service_DeveloperGarden_ConferenceCall_Mock::ENV_MOCK
        );
        $this->service = new Zend_Service_DeveloperGarden_ConferenceCall_Mock($config);
    }

    public function testCreateConference()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );
        $this->assertNotNull($result->getConferenceId());
        $this->assertTrue($result->isValid());
        $this->assertEquals('0000', $result->getErrorCode());
        $this->assertNotNull($result->getErrorMessage());
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testCreateConferenceWrongDurationException()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            -1
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );
    }

    public function testUpdateConference()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $conferenceDetails->setName('Marco Kaiser')
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $conferenceDetails->setDescription('Zend Framework')
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $conferenceDetails->setDuration(600)
        );


        $result = $this->service->updateConference(
            $conferenceId,
            'My Name',
            $conferenceDetails
        );

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );
        $this->assertEquals('0000', $result->getStatusCode());
        $this->assertNotNull($result->getStatusMessage());
    }

    public function testNewParticipant()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $this->assertNotNull($result->getParticipantId());

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0002',
            'bate@php.net'
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $this->assertNotNull($result->getParticipantId());
    }

    public function testUpdateParticipant()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $participantId = $result->getParticipantId();
        $this->assertNotNull($participantId);

        foreach ($this->service->getParticipantActions() as $k => $v) {
            $result = $this->service->updateParticipant(
                $conferenceId,
                $participantId,
                $k,
                $participant
            );
            $this->assertType(
                'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
                $result
            );
        }
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testUpdateParticipantException()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $participantId = $result->getParticipantId();
        $this->assertNotNull($participantId);

        $this->assertNull($this->service->updateParticipant(
            $conferenceId,
            $participantId,
            -1,
            $participant
        ));
    }

    public function testUpdateParticipantCheckStatus()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $participantId = $result->getParticipantId();
        $this->assertNotNull($participantId);

        $result = $this->service->updateParticipant(
            $conferenceId,
            $participantId,
            Zend_Service_DeveloperGarden_ConferenceCall_Mock::PARTICIPANT_MUTE_ON,
            $participant
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );

        $result = $this->service->getParticipantStatus($conferenceId, $participantId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetParticipantStatusResponseType',
            $result
        );
        $status = $result->getStatus();
        $this->assertType(
            'array',
            $status
        );
        foreach ($status as $k => $v ) {
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantStatus',
                $v
            );
            switch ($v->getName()) {
                case 'muted' : {
                    $this->assertEquals('true', $v->getValue());
                    break;
                }
            }
        }
    }

    public function testRemoveConference()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $this->assertNotNull($result->getParticipantId());

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0002',
            'bate@php.net'
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $this->assertNotNull($result->getParticipantId());

        $result = $this->service->removeConference($conferenceId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );
    }

    public function testRemoveParticipantFromConference()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part1Id = $result->getParticipantId();
        $this->assertNotNull($part1Id);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0002',
            'bate@php.net'
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part2Id = $result->getParticipantId();
        $this->assertNotNull($part2Id);

        $result = $this->service->removeParticipant($conferenceId, $part1Id);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );

        $result = $this->service->removeParticipant($conferenceId, $part2Id);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );
    }

    public function testCommitConference()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part1Id = $result->getParticipantId();
        $this->assertNotNull($part1Id);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0002',
            'bate@php.net'
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part2Id = $result->getParticipantId();
        $this->assertNotNull($part2Id);

        $result = $this->service->commitConference($conferenceId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testCommitConferenceWithOutInitiator()
    {
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_Mock',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_ConferenceCall_Mock::ENV_SANDBOX)
        );
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            30
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0002',
            'bate@php.net'
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part2Id = $result->getParticipantId();
        $this->assertNotNull($part2Id);

        $result = $this->service->commitConference($conferenceId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testCommitConferenceWithThresholdException()
    {
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_Mock',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_ConferenceCall_Mock::ENV_SANDBOX)
        );
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $this->assertNull($this->service->createConference(
            $conferenceDetails->getName(),
            $conferenceDetails
        ));
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testMockBugGetConferenceList()
    {
        $this->assertNull($this->service->getConferenceList(0, 'My Name'));
    }

    public function testGetConferenceList()
    {
        //$this->markTestSkipped('Throws internal error on mock environment');
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_Mock',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_ConferenceCall_Mock::ENV_SANDBOX)
        );
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            30
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $result = $this->service->getConferenceList(0, 'My Name');
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceListResponseType',
            $result
        );

        $this->assertType(
            'array',
            $result->getConferenceIds()
        );
        $this->assertTrue(count($result->getConferenceIds()) > 0);
    }

    public function testGetRunningConference()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part1Id = $result->getParticipantId();
        $this->assertNotNull($part1Id);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0002',
            'bate@php.net'
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part2Id = $result->getParticipantId();
        $this->assertNotNull($part2Id);

        $result = $this->service->commitConference($conferenceId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );

        $result = $this->service->getRunningConference($conferenceId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetRunningConferenceResponseType',
            $result
        );
        $this->assertNotNull($result->getConferenceId());
    }

    public function testGetConferenceStatusSandBox()
    {
        //$this->markTestSkipped('Throws internal error on mock environment');
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_Mock',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_ConferenceCall_Mock::ENV_SANDBOX)
        );
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            30
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part1Id = $result->getParticipantId();
        $this->assertNotNull($part1Id);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0002',
            'bate@php.net'
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part2Id = $result->getParticipantId();
        $this->assertNotNull($part2Id);

        $result = $this->service->getConferenceStatus($conferenceId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceStatusResponseType',
            $result
        );

        $detail = $result->getDetail();
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $detail
        );
        $this->assertEquals('This is the Conference Description', $detail->getDescription());
        $this->assertEquals(30, $detail->getDuration());
        $this->assertEquals('My Name', $detail->getName());

        $this->assertNull($result->getSchedule());
        $this->assertEquals(0, $result->getStartTime());

        $this->assertType('array', $result->getParticipants());
        $this->assertTrue(count($result->getParticipants()) === 2);
        foreach ($result->getParticipants() as $v) {
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_Participant',
                $v
            );
        }

        if ($this->service->getEnvironment() === Zend_Service_DeveloperGarden_ConferenceCall::ENV_PRODUCTION) {
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceAccount',
                $result->getAccount()
            );
        }
    }

    public function testGetConferenceStatusMock()
    {
        //$this->markTestSkipped('Throws internal error on mock environment');
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            30
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part1Id = $result->getParticipantId();
        $this->assertNotNull($part1Id);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0002',
            'bate@php.net'
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part2Id = $result->getParticipantId();
        $this->assertNotNull($part2Id);

        try {
            $result = $this->service->getConferenceStatus($conferenceId);
        } catch (Zend_Service_DeveloperGarden_Response_Exception $e) {
            if ($e->getMessage() == 'Internal Error') {
                $this->markTestSkipped('Internal Error still exists on MOCK!');
            }
        }
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceStatusResponseType',
            $result
        );

        $detail = $result->getDetail();
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $detail
        );
        $this->assertEquals('This is the Conference Description', $detail->getDescription());
        $this->assertEquals(30, $detail->getDuration());
        $this->assertEquals('My Name', $detail->getName());

        $this->assertNull($result->getSchedule());
        $this->assertEquals(0, $result->getStartTime());

        $this->assertType('array', $result->getParticipants());
        $this->assertTrue(count($result->getParticipants()) === 2);
        foreach ($result->getParticipants() as $v) {
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_Participant',
                $v
            );
        }

        if ($this->service->getEnvironment() === Zend_Service_DeveloperGarden_ConferenceCall::ENV_PRODUCTION) {
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceAccount',
                $result->getAccount()
            );
        }
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testGetConferenceStatusException()
    {
        //$this->markTestSkipped('Throws internal error on mock environment');
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_Mock',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_ConferenceCall_Mock::ENV_SANDBOX)
        );
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $this->assertNull($this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            ));
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

    }

    public function testGetParticipantStatus()
    {
        //$this->markTestSkipped('Throws internal error on mock environment');
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        try {
            $result = $this->service->createConference(
                $conferenceDetails->getName(),
                $conferenceDetails
            );
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }

        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
            $result
        );

        $conferenceId = $result->getConferenceId();
        $this->assertNotNull($conferenceId);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0001',
            'bate@php.net',
            true
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part1Id = $result->getParticipantId();
        $this->assertNotNull($part1Id);

        $participant = new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
            'Marco',
            'Kaiser',
            '+49 32 0000 0002',
            'bate@php.net'
        );

        $result = $this->service->newParticipant($conferenceId, $participant);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
            $result
        );
        $part2Id = $result->getParticipantId();
        $this->assertNotNull($part2Id);

        $result = $this->service->getParticipantStatus($conferenceId, $part1Id);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetParticipantStatusResponseType',
            $result
        );
        $this->assertType('array', $result->getStatus());
        foreach ($result->getStatus() as $v) {
            $this->assertType('Zend_Service_DeveloperGarden_ConferenceCall_ParticipantStatus', $v);
            $this->assertNotNull($v->getName());
            $this->assertNotNull($v->getValue());
        }
    }

    public function testRemoveConferenceLooped()
    {
        //$this->markTestSkipped('Throws internal error on mock environment');
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_Mock',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_ConferenceCall_Mock::ENV_SANDBOX)
        );
        $result = $this->service->getConferenceList(0, 'My Name');
        foreach ($result->getConferenceIds() as $k => $v) {
            $this->assertNotNull($v);
            $this->assertType(
                'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
                $this->service->removeConference($v)
            );
        }
    }

    /**
     * Conference Template API Tests
     */

    public function testCreateConferenceTemplate()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->createConferenceTemplate(
            $conferenceDetails->getName(),
            $conferenceDetails,
            $participants
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
            $result
        );

        $templateId = $result->getTemplateId();
        $this->assertNotNull($templateId);
    }

    public function testRemoveConferenceTemplate()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->createConferenceTemplate(
            $conferenceDetails->getName(),
            $conferenceDetails,
            $participants
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
            $result
        );

        $templateId = $result->getTemplateId();
        $this->assertNotNull($templateId);

        $result = $this->service->removeConferenceTemplate($templateId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );
        $this->assertEquals('0000', $result->getStatusCode());
    }

    public function testUpdateConferenceTemplate()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->createConferenceTemplate(
            $conferenceDetails->getName(),
            $conferenceDetails,
            $participants
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
            $result
        );

        $templateId = $result->getTemplateId();
        $this->assertNotNull($templateId);

        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $conferenceDetails->setDescription('Some Description')
        );

        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $conferenceDetails->setDuration(123)
        );

        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $conferenceDetails->setName('Marco Kaiser')
        );

        $result = $this->service->updateConferenceTemplate(
            $templateId,
            null,
            $conferenceDetails
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );
        $this->assertEquals('0000', $result->getStatusCode());
    }

    public function testGetConferenceTemplate()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->createConferenceTemplate(
            $conferenceDetails->getName(),
            $conferenceDetails,
            $participants
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
            $result
        );

        $templateId = $result->getTemplateId();
        $this->assertNotNull($templateId);

        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $conferenceDetails->setDescription('Some Description')
        );

        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $conferenceDetails->setDuration(123)
        );

        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $conferenceDetails->setName('Marco Kaiser')
        );

        $result = $this->service->updateConferenceTemplate(
            $templateId,
            null,
            $conferenceDetails
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
            $result
        );
        $this->assertEquals('0000', $result->getStatusCode());

        $result = $this->service->getConferenceTemplate($templateId);

        $detail = $result->getDetail();
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
            $detail
        );
        $this->assertEquals('Marco Kaiser', $detail->getName());
        $this->assertEquals('Some Description', $detail->getDescription());
        $this->assertEquals(123, $detail->getDuration());

        $this->assertType('array', $result->getParticipants());
        $this->assertTrue(count($result->getParticipants()) > 0);
        foreach ($result->getParticipants() as $k => $v) {
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_Participant',
                $v
            );
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail',
                $v->getDetail()
            );
            $this->assertType(
                'array',
                $v->getStatus()
            );
        }

        $pid1 = $result->getParticipantById('pid1');
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_Participant',
            $pid1
        );

        $this->assertEquals('pid1', $pid1->getParticipantId());
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail',
            $pid1->getDetail()
        );
        $this->assertType('array', $pid1->getStatus());
    }

    public function testGetConferenceTemplateList()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->createConferenceTemplate(
            $conferenceDetails->getName(),
            $conferenceDetails,
            $participants
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
            $result
        );

        $templateId = $result->getTemplateId();
        $this->assertNotNull($templateId);

        $result = $this->service->getConferenceTemplateList('My Name');
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateListResponseType',
            $result
        );
        $list = $result->getTemplateIds();
        $this->assertType('array', $list);
        foreach ($list as $k => $v) {
            $this->assertNotNull($v);
        }
    }

    public function testAddConferenceTemplateParticipant()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->createConferenceTemplate(
            $conferenceDetails->getName(),
            $conferenceDetails
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
            $result
        );

        $templateId = $result->getTemplateId();
        $this->assertNotNull($templateId);

        $this->assertType('array', $participants);
        foreach ($participants as $k => $v) {
            $result = $this->service->addConferenceTemplateParticipant($templateId, $v);
            $this->assertType(
                'Zend_Service_DeveloperGarden_Response_ConferenceCall_AddConferenceTemplateParticipantResponseType',
                $result
            );
            $this->assertEquals('pid'. ($k+1), $result->getParticipantId());
            $this->assertEquals('0000', $result->getStatusCode());
        }
    }

    public function testRemoveConferenceTemplateParticipant()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->createConferenceTemplate(
            $conferenceDetails->getName(),
            $conferenceDetails,
            $participants
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
            $result
        );

        $templateId = $result->getTemplateId();
        $this->assertNotNull($templateId);

        $pidArray = array('pid1', 'pid2');
        foreach ($pidArray as $k => $v) {
            $result = $this->service->removeConferenceTemplateParticipant($templateId, $v);
            $this->assertType(
                'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
                $result
            );
            $this->assertEquals('0000', $result->getStatusCode());
        }
        $result = $this->service->getConferenceTemplate($templateId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateResponseType',
            $result
        );
        $this->assertEquals(0, count($result->getParticipants()));
    }

    public function testGetConferenceTemplateParticipant()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->createConferenceTemplate(
            $conferenceDetails->getName(),
            $conferenceDetails,
            $participants
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
            $result
        );

        $templateId = $result->getTemplateId();
        $this->assertNotNull($templateId);

        $pidArray = array('pid1', 'pid2');
        foreach ($pidArray as $k => $v) {
            $result = $this->service->getConferenceTemplateParticipant($templateId, $v);
            $this->assertType(
                'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateParticipantResponseType',
                $result
            );
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail',
                $result->getParticipant()
            );
            $this->assertEquals('0000', $result->getStatusCode());
        }
    }

    public function testUpdateConferenceTemplateParticipant()
    {
        // works only on sandbox
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_Mock',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_ConferenceCall_Mock::ENV_SANDBOX)
        );

        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            10
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->createConferenceTemplate(
            $conferenceDetails->getName(),
            $conferenceDetails,
            $participants
        );
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
            $result
        );

        $templateId = $result->getTemplateId();
        $this->assertNotNull($templateId);

        $result = $this->service->getConferenceTemplate($templateId);
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateResponseType',
            $result
        );
        $list = $result->getParticipants();
        $this->assertType('array', $list);

        foreach ($list as $k => $v) {
            $participantId = $v->getParticipantId();
            $detail = $v->getDetail();
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail',
                $detail
            );
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail',
                $detail->setFirstName('Zend')
            );
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail',
                $detail->setLastName('Framework')
            );

            $result = $this->service->updateConferenceTemplateParticipant(
                $templateId,
                $participantId,
                $detail
            );
            $this->assertType(
                'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
                $result
            );
            $this->assertEquals('0000', $result->getStatusCode());

            // ask for part
            $result = $this->service->getConferenceTemplateParticipant($templateId, $participantId);
            $this->assertType(
                'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateParticipantResponseType',
                $result
            );
            $newPart = $result->getParticipant();
            $this->assertType(
                'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail',
                $newPart
            );
            $this->assertEquals('Zend', $newPart->getFirstName());
            $this->assertEquals('Framework', $newPart->getLastName());
        }

        $this->service->removeConferenceTemplate($templateId);
    }

    public function testRemoveConferenceTemplateLoopedMock()
    {
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->getConferenceTemplateList('My Name');
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateListResponseType',
            $result
        );
        $list = $result->getTemplateIds();
        $this->assertType('array', $list);
        foreach ($list as $k => $v) {
            $templateId = $v;
            $this->assertNotNull($templateId);
            $result = $this->service->removeConferenceTemplate($templateId);
            $this->assertType(
                'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
                $result
            );
            $this->assertEquals('0000', $result->getStatusCode());
        }
    }

    public function testRemoveConferenceTemplateLoopedSandbox()
    {
        $this->assertType(
            'Zend_Service_DeveloperGarden_ConferenceCall_Mock',
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_ConferenceCall_Mock::ENV_SANDBOX)
        );
        $conferenceDetails = new Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail(
            'My Name',
            'This is the Conference Description',
            300
        );

        $participants = array(
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0001',
                'bate@php.net',
                true
            ),
            new Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail(
                'Marco',
                'Kaiser',
                '+49 32 0000 0002',
                'bate@php.net'
            ),
        );

        $result = $this->service->getConferenceTemplateList('My Name');
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateListResponseType',
            $result
        );
        $list = $result->getTemplateIds();
        $this->assertType('array', $list);
        foreach ($list as $k => $v) {
            $templateId = $v;
            $this->assertNotNull($templateId);
            $result = $this->service->removeConferenceTemplate($templateId);
            $this->assertType(
                'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',
                $result
            );
            $this->assertEquals('0000', $result->getStatusCode());
        }
    }
}

class Zend_Service_DeveloperGarden_ConferenceCall_Mock
    extends Zend_Service_DeveloperGarden_ConferenceCall
{

}
if (PHPUnit_MAIN_METHOD == 'Zend_Service_DeveloperGarden_ConferenceCallTest::main') {
    Zend_Service_DeveloperGarden_ConferenceCallTest::main();
}
