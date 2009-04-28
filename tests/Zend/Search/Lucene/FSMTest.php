<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_FSM
 */
require_once 'Zend/Search/Lucene/FSM.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';



class Zend_Search_Lucene_FSM_testClass
{
    public $action1Passed = false;
    public $action2Passed = false;
    public $action3Passed = false;
    public $action4Passed = false;
    public $action5Passed = false;
    public $action6Passed = false;
    public $action7Passed = false;
    public $action8Passed = false;

    public function action1()  { $this->action1Passed = true; }
    public function action2()  { $this->action2Passed = true; }
    public function action3()  { $this->action3Passed = true; }
    public function action4()  { $this->action4Passed = true; }
    public function action5()  { $this->action5Passed = true; }
    public function action6()  { $this->action6Passed = true; }
    public function action7()  { $this->action7Passed = true; }
    public function action8()  { $this->action8Passed = true; }
}

class Zend_Search_Lucene_FSM_testFSMClass extends Zend_Search_Lucene_FSM
{
    const OPENED            = 0;
    const CLOSED            = 1;
    const CLOSED_AND_LOCKED = 2;

    const OPENED_AND_LOCKED = 3; // Wrong state, should not be used


    const OPEN   = 0;
    const CLOSE  = 1;
    const LOCK   = 3;
    const UNLOCK = 4;

    /**
     * Object to trace FSM actions
     *
     * @var Zend_Search_Lucene_FSM_testClass
     */
    public $actionTracer;

    public function __construct()
    {
        $this->actionTracer = new Zend_Search_Lucene_FSM_testClass();

        $this->addStates(array(self::OPENED, self::CLOSED, self::CLOSED_AND_LOCKED));
        $this->addInputSymbols(array(self::OPEN, self::CLOSE, self::LOCK, self::UNLOCK));

        $unlockAction     = new Zend_Search_Lucene_FSMAction($this->actionTracer, 'action4');
        $openAction       = new Zend_Search_Lucene_FSMAction($this->actionTracer, 'action6');
        $closeEntryAction = new Zend_Search_Lucene_FSMAction($this->actionTracer, 'action2');
        $closeExitAction  = new Zend_Search_Lucene_FSMAction($this->actionTracer, 'action8');

        $this->addRules(array( array(self::OPENED,            self::CLOSE,  self::CLOSED),
                               array(self::CLOSED,            self::OPEN,   self::OPEN),
                               array(self::CLOSED,            self::LOCK,   self::CLOSED_AND_LOCKED),
                               array(self::CLOSED_AND_LOCKED, self::UNLOCK, self::CLOSED, $unlockAction),
                             ));

        $this->addInputAction(self::CLOSED_AND_LOCKED, self::UNLOCK, $unlockAction);

        $this->addTransitionAction(self::CLOSED, self::OPENED, $openAction);

        $this->addEntryAction(self::CLOSED, $closeEntryAction);

        $this->addExitAction(self::CLOSED, $closeExitAction);
    }
}


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_FSMTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $doorFSM = new Zend_Search_Lucene_FSM_testFSMClass();

        $this->assertTrue($doorFSM instanceof Zend_Search_Lucene_FSM);
        $this->assertEquals($doorFSM->getState(), Zend_Search_Lucene_FSM_testFSMClass::OPENED);
    }

    public function testSetState()
    {
        $doorFSM = new Zend_Search_Lucene_FSM_testFSMClass();

        $this->assertEquals($doorFSM->getState(), Zend_Search_Lucene_FSM_testFSMClass::OPENED);

        $doorFSM->setState(Zend_Search_Lucene_FSM_testFSMClass::CLOSED_AND_LOCKED);
        $this->assertEquals($doorFSM->getState(), Zend_Search_Lucene_FSM_testFSMClass::CLOSED_AND_LOCKED );

        $wrongStateExceptionCatched = false;
        try {
            $doorFSM->setState(Zend_Search_Lucene_FSM_testFSMClass::OPENED_AND_LOCKED);
        } catch(Zend_Search_Exception $e) {
            $wrongStateExceptionCatched = true;
        }
        $this->assertTrue($wrongStateExceptionCatched);
    }

    public function testReset()
    {
        $doorFSM = new Zend_Search_Lucene_FSM_testFSMClass();

        $doorFSM->setState(Zend_Search_Lucene_FSM_testFSMClass::CLOSED_AND_LOCKED);
        $this->assertEquals($doorFSM->getState(), Zend_Search_Lucene_FSM_testFSMClass::CLOSED_AND_LOCKED);

        $doorFSM->reset();
        $this->assertEquals($doorFSM->getState(), Zend_Search_Lucene_FSM_testFSMClass::OPENED);
    }

    public function testProcess()
    {
        $doorFSM = new Zend_Search_Lucene_FSM_testFSMClass();

        $doorFSM->process(Zend_Search_Lucene_FSM_testFSMClass::CLOSE);
        $this->assertEquals($doorFSM->getState(), Zend_Search_Lucene_FSM_testFSMClass::CLOSED);

        $doorFSM->process(Zend_Search_Lucene_FSM_testFSMClass::LOCK);
        $this->assertEquals($doorFSM->getState(), Zend_Search_Lucene_FSM_testFSMClass::CLOSED_AND_LOCKED);

        $doorFSM->process(Zend_Search_Lucene_FSM_testFSMClass::UNLOCK);
        $this->assertEquals($doorFSM->getState(), Zend_Search_Lucene_FSM_testFSMClass::CLOSED);

        $doorFSM->process(Zend_Search_Lucene_FSM_testFSMClass::OPEN);
        $this->assertEquals($doorFSM->getState(), Zend_Search_Lucene_FSM_testFSMClass::OPENED);

        $wrongInputExceptionCatched = false;
        try {
            $doorFSM->process(Zend_Search_Lucene_FSM_testFSMClass::LOCK);
        } catch(Zend_Search_Exception $e) {
            $wrongInputExceptionCatched = true;
        }
        $this->assertTrue($wrongInputExceptionCatched);
    }

    public function testActions()
    {
        $doorFSM = new Zend_Search_Lucene_FSM_testFSMClass();

        $this->assertFalse($doorFSM->actionTracer->action2Passed /* 'closed' state entry action*/);
        $doorFSM->process(Zend_Search_Lucene_FSM_testFSMClass::CLOSE);
        $this->assertTrue($doorFSM->actionTracer->action2Passed);

        $this->assertFalse($doorFSM->actionTracer->action8Passed /* 'closed' state exit action*/);
        $doorFSM->process(Zend_Search_Lucene_FSM_testFSMClass::LOCK);
        $this->assertTrue($doorFSM->actionTracer->action8Passed);

        $this->assertFalse($doorFSM->actionTracer->action4Passed /* 'closed&locked' state +'unlock' input action */);
        $doorFSM->process(Zend_Search_Lucene_FSM_testFSMClass::UNLOCK);
        $this->assertTrue($doorFSM->actionTracer->action4Passed);

        $this->assertFalse($doorFSM->actionTracer->action6Passed /* 'locked' -> 'opened' transition action action */);
        $doorFSM->process(Zend_Search_Lucene_FSM_testFSMClass::OPEN);
        $this->assertTrue($doorFSM->actionTracer->action6Passed);
    }
}

