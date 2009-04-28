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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Calendar.php';
require_once 'Zend/Gdata/Calendar/EventQuery.php';
require_once 'Zend/Http/Client.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Calendar_EventQueryExceptionTest extends PHPUnit_Framework_TestCase
{
    
    const GOOGLE_DEVELOPER_CALENDAR = 'developer-calendar@google.com';

    public function setUp()
    {
        $this->query = new Zend_Gdata_Calendar_EventQuery();
    }

    /**
     * @expectedException Zend_Gdata_App_Exception
     */
    public function testSingleEventsThrowsExceptionOnSetInvalidValue()
    {
        $this->query->resetParameters();
        $singleEvents = 'puppy';        
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setSingleEvents($singleEvents);
    }

    /**
     * @expectedException Zend_Gdata_App_Exception
     */
    public function testFutureEventsThrowsExceptionOnSetInvalidValue()
    {
        $this->query->resetParameters();
        $futureEvents = 'puppy';        
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setFutureEvents($futureEvents);
    }
    
}
