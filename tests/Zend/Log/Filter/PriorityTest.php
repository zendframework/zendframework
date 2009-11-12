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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log */
require_once 'Zend/Log.php';

/** Zend_Log_Filter_Priority */
require_once 'Zend/Log/Filter/Priority.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class Zend_Log_Filter_PriorityTest extends PHPUnit_Framework_TestCase
{
    public function testComparisonDefaultsToLessThanOrEqual()
    {
        // accept at or below priority 2
        $filter = new Zend_Log_Filter_Priority(2);

        $this->assertTrue($filter->accept(array('priority' => 2)));
        $this->assertTrue($filter->accept(array('priority' => 1)));
        $this->assertFalse($filter->accept(array('priority' => 3)));
    }

    public function testComparisonOperatorCanBeChanged()
    {
        // accept above priority 2
        $filter = new Zend_Log_Filter_Priority(2, '>');

        $this->assertTrue($filter->accept(array('priority' => 3)));
        $this->assertFalse($filter->accept(array('priority' => 2)));
        $this->assertFalse($filter->accept(array('priority' => 1)));
    }

    public function testConstructorThrowsOnInvalidPriority()
    {
        try {
            new Zend_Log_Filter_Priority('foo');
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegExp('/must be an integer/i', $e->getMessage());
        }
    }

}
