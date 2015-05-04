<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Writer;

use ZendTest\Log\TestAsset\MockChromePhp;
use Zend\Log\Writer\ChromePhp;
use Zend\Log\Logger;

/**
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
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
        $this->assertInstanceOf('Zend\Log\Writer\ChromePhp\ChromePhpInterface', $writer->getChromePhp());
    }

    public function testSetChromePhp()
    {
        $writer   = new ChromePhp($this->chromephp);
        $chromephp2 = new MockChromePhp();

        $writer->setChromePhp($chromephp2);
        $this->assertInstanceOf('Zend\Log\Writer\ChromePhp\ChromePhpInterface', $writer->getChromePhp());
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
        $this->assertEmpty($this->chromephp->calls);
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
        $this->assertInstanceOf('Zend\Log\Writer\ChromePhp\ChromePhpInterface', $writer->getChromePhp());
        $this->assertAttributeInstanceOf('Zend\Log\Formatter\ChromePhp', 'formatter', $writer);

        $filters = self::readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertEquals($filter, $filters[0]);
    }
}
