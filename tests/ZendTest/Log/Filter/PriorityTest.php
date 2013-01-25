<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Filter;

use Zend\Log\Filter\Priority;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class PriorityTest extends \PHPUnit_Framework_TestCase
{
    public function testComparisonDefaultsToLessThanOrEqual()
    {
        // accept at or below priority 2
        $filter = new Priority(2);

        $this->assertTrue($filter->filter(array('priority' => 2)));
        $this->assertTrue($filter->filter(array('priority' => 1)));
        $this->assertFalse($filter->filter(array('priority' => 3)));
    }

    public function testComparisonOperatorCanBeChanged()
    {
        // accept above priority 2
        $filter = new Priority(2, '>');

        $this->assertTrue($filter->filter(array('priority' => 3)));
        $this->assertFalse($filter->filter(array('priority' => 2)));
        $this->assertFalse($filter->filter(array('priority' => 1)));
    }

    public function testConstructorThrowsOnInvalidPriority()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'must be an integer');
        new Priority('foo');
    }
}
