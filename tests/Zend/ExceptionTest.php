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
 * @package    Zend_Exception
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

require_once 'Zend/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Exception
 * @subpackage UnitTests
 * @group      Zend_Exception
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_ExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorDefaults()
    {
        $e = new Zend_Exception();
        $this->assertEquals('', $e->getMessage());
        $this->assertEquals(0, $e->getCode());
        $this->assertNull($e->getPrevious());
    }

    public function testMessage()
    {
        $e = new Zend_Exception('msg');
        $this->assertEquals('msg', $e->getMessage());
    }

    public function testCode()
    {
        $e = new Zend_Exception('msg', 100);
        $this->assertEquals(100, $e->getCode());
    }

    public function testPrevious()
    {
        $p = new Zend_Exception('p', 0);
        $e = new Zend_Exception('e', 0, $p);
        $this->assertEquals($p, $e->getPrevious());
    }

    public function testToString()
    {
        $p = new Zend_Exception('p', 0);
        $e = new Zend_Exception('e', 0, $p);
        $s = $e->__toString();
        $this->assertContains('p', $s);
        $this->assertContains('Next', $s);
        $this->assertContains('e', $s);
    }
}
