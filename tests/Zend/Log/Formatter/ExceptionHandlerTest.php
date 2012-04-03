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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Formatter;

use Zend\Log\Formatter\ExceptionHandler;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $date = date('c');
        $event = array(
            'timestamp'    => $date,
            'message'      => 'test',
            'priority'     => 1,
            'priorityName' => 'CRIT',
            'extra' => array (
                'file'  => 'test.php',
                'line'  => 1,
                'trace' => array(array(
                    'file'     => 'test.php',
                    'line'     => 1,
                    'function' => 'test',
                    'class'    => 'Test',
                    'type'     => '::',
                    'args'     => array(1)
                ))
            )
        );
        $formatter = new ExceptionHandler();
        $output = $formatter->format($event);

        $this->assertEquals($date . " CRIT (1) test in test.php on line 1\n" .
                "[Trace]\nFile  : test.php\nLine  : 1\nFunc  : test\nClass : Test\n" .
                "Type  : static\nArgs  : Array\n(\n    [0] => 1\n)\n\n", $output);
    }

    public function testFactory()
    {
        $options = array();
        $formatter = ExceptionHandler::factory($options);
        $this->assertInstanceOf('Zend\Log\Formatter\ExceptionHandler', $formatter);
    }
}
