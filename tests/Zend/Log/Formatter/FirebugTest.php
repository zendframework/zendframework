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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Formatter;

use Zend\Log\Formatter\Firebug;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class FirebugTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $event = array(
            'timestamp' => date('c'),
        	'message' => 'tottakai',
            'priority' => 2,
        	'priorityName' => 'CRIT'
        );
        $formatter = new Firebug();
        $output = $formatter->format($event);

        $this->assertEquals('tottakai', $output);
    }

    /**
     * @group ZF-9176
     */
    public function testFactory()
    {
        $options = array();
        $formatter = Firebug::factory($options);
        $this->assertInstanceOf('Zend\Log\Formatter\Firebug', $formatter);
    }
}
