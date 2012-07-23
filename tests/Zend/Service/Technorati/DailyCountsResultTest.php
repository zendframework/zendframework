<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Technorati;

use DateTime;
use Zend\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class DailyCountsResultTest extends TestCase
{
    public function setUp()
    {
        $this->domElements = self::getTestFileElementsAsDom('TestDailyCountsResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\CosmosResult', array($this->domElements->item(0)));
    }

    public function testDailyCountsResult()
    {
        $object = new Technorati\DailyCountsResult($this->domElements->item(1));

        // check properties
        $this->assertInstanceOf('DateTime', $object->getDate());
        $this->assertEquals(new DateTime('2007-11-13'), $object->getDate());
        $this->assertInternalType('integer', $object->getCount());
        $this->assertEquals(54414, $object->getCount());
    }

    public function testDailyCountsResultSerialization()
    {
        $this->_testResultSerialization(new Technorati\DailyCountsResult($this->domElements->item(0)));
    }
}
