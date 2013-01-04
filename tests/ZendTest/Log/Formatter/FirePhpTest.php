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
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Formatter;

use Zend\Log\Formatter\FirePhp;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class FirePhpTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatWithExtraData()
    {
        $fields = array( 'message' => 'foo',
                'extra' => new \stdClass() );

        $f = new FirePhp();
        list($line, $label) = $f->format($fields);

        $this->assertContains($fields['message'], $label);
        $this->assertEquals($fields['extra'], $line);
    }

    public function testFormatWithoutExtra()
    {
        $fields = array( 'message' => 'foo' );

        $f = new FirePhp();
        list($line, $label) = $f->format($fields);

        $this->assertContains($fields['message'], $line);
        $this->assertNull($label);
    }

    public function testFormatWithEmptyExtra()
    {
        $fields = array( 'message' => 'foo',
                'extra' => array() );

        $f = new FirePhp();
        list($line, $label) = $f->format($fields);

        $this->assertContains($fields['message'], $line);
        $this->assertNull($label);
    }

    public function testSetDateTimeFormatDoesNothing()
    {
        $formatter = new FirePhp();

        $this->assertEquals('', $formatter->getDateTimeFormat());
        $this->assertSame($formatter, $formatter->setDateTimeFormat('r'));
        $this->assertEquals('', $formatter->getDateTimeFormat());
    }
}
