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

namespace ZendTest\Log\Writer;

use ZendTest\Log\TestAsset\MockChromePhp;
use Zend\Log\Writer\ChromePhp;
use Zend\Log\Writer\ChromePhp\ChromePhpInterface;
use Zend\Log\Logger;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class ChromePhpTest extends \PHPUnit_Framework_TestCase
{
    protected $chromephp;

    public function setUp()
    {
        $this->chromephp = new MockChromePhp();

    }

    public function testGetChromePhp()
    {
        $writer = new ChromePhp($this->chromephp);
        $this->assertTrue($writer->getChromePhp() instanceof ChromePhpInterface);
    }

    public function testSetChromePhp()
    {
        $writer   = new ChromePhp($this->chromephp);
        $chromephp2 = new MockChromePhp();

        $writer->setChromePhp($chromephp2);
        $this->assertTrue($writer->getChromePhp() instanceof ChromePhpInterface);
        $this->assertEquals($chromephp2, $writer->getChromePhp());
    }

    public function testWrite()
    {
        $writer = new ChromePhp($this->chromephp);
        $writer->write(array(
            'message' => 'my msg',
            'priority' => Logger::DEBUG
        ));
        $this->assertEquals('my msg', $this->chromephp->calls['trace'][0]);
    }

    public function testWriteDisabled()
    {
        $chromephp = new MockChromePhp(false);
        $writer = new ChromePhp($chromephp);
        $writer->write(array(
            'message' => 'my msg',
            'priority' => Logger::DEBUG
        ));
        $this->assertTrue(empty($this->chromephp->calls));
    }

    public function testConstructWithOptions()
    {
        $formatter = new \Zend\Log\Formatter\Simple();
        $filter    = new \Zend\Log\Filter\Mock();
        $writer = new ChromePhp(array(
            'filters'   => $filter,
            'formatter' => $formatter,
            'instance'  => $this->chromephp,
        ));
        $this->assertTrue($writer->getChromePhp() instanceof ChromePhpInterface);
        $this->assertAttributeInstanceOf('Zend\Log\Formatter\ChromePhp', 'formatter', $writer);

        $filters = self::readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertEquals($filter, $filters[0]);
    }
}
