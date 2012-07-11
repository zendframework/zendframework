<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Formatter;

use Zend\Log\Formatter\ExceptionHandler;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $event = array(
            'timestamp'    => '2012-06-12T09:00:00+02:00',
            'message'      => 'test',
            'priority'     => 1,
            'priorityName' => 'CRIT',
            'extra' => array (
                'file'  => 'test.php',
                'line'  => 1,
                'trace' => array(
                    array(
                        'file'     => 'test.php',
                        'line'     => 1,
                        'function' => 'test',
                        'class'    => 'Test',
                        'type'     => '::',
                        'args'     => array(1)
                    ),
                    array(
                        'file'     => 'test.php',
                        'line'     => 2,
                        'function' => 'test',
                        'class'    => 'Test',
                        'type'     => '::',
                        'args'     => array(1)
                    )
                )
            )
        );
        $expected = <<<EOF
2012-06-12T09:00:00+02:00 CRIT (1) test in test.php on line 1
[Trace]
File  : test.php
Line  : 1
Func  : test
Class : Test
Type  : static
Args  : Array
(
    [0] => 1
)

File  : test.php
Line  : 2
Func  : test
Class : Test
Type  : static
Args  : Array
(
    [0] => 1
)


EOF;

        $formatter = new ExceptionHandler();
        $output = $formatter->format($event);

        $this->assertEquals($expected, $output);
    }
}
