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

/** Zend_Log_Formatter_Simple */

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class Zend_Log_Formatter_SimpleTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorThrowsOnBadFormatString()
    {
        try {
            new Zend_Log_Formatter_Simple(1);
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegExp('/must be a string/i', $e->getMessage());
        }
    }

    public function testDefaultFormat()
    {
        $fields = array('timestamp'    => 0,
                        'message'      => 'foo',
                        'priority'     => 42,
                        'priorityName' => 'bar');

        $f = new Zend_Log_Formatter_Simple();
        $line = $f->format($fields);

        $this->assertContains((string)$fields['timestamp'], $line);
        $this->assertContains($fields['message'], $line);
        $this->assertContains($fields['priorityName'], $line);
        $this->assertContains((string)$fields['priority'], $line);
    }

    function testComplexValues()
    {
        $fields = array('timestamp'    => 0,
                        'priority'     => 42,
                        'priorityName' => 'bar');

        $f = new Zend_Log_Formatter_Simple();

        $fields['message'] = 'Foo';
        $line = $f->format($fields);
        $this->assertContains($fields['message'], $line);

        $fields['message'] = 10;
        $line = $f->format($fields);
        $this->assertContains($fields['message'], $line);

        $fields['message'] = 10.5;
        $line = $f->format($fields);
        $this->assertContains($fields['message'], $line);

        $fields['message'] = true;
        $line = $f->format($fields);
        $this->assertContains('1', $line);

        $fields['message'] = fopen('php://stdout', 'w');
        $line = $f->format($fields);
        $this->assertContains('Resource id ', $line);
        fclose($fields['message']);

        $fields['message'] = range(1,10);
        $line = $f->format($fields);
        $this->assertContains('array', $line);

        $fields['message'] = new Zend_Log_Formatter_SimpleTest_TestObject1();
        $line = $f->format($fields);
        $this->assertContains($fields['message']->__toString(), $line);

        $fields['message'] = new Zend_Log_Formatter_SimpleTest_TestObject2();
        $line = $f->format($fields);
        $this->assertContains('object', $line);
    }
}

class Zend_Log_Formatter_SimpleTest_TestObject1 {

    public function __toString()
    {
        return 'Hello World';
    }
}

class Zend_Log_Formatter_SimpleTest_TestObject2 {
}
