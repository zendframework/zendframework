<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Writer;

use ZendTest\Log\TestAsset\MockFirePhp;
use Zend\Log\Writer\FirePhp;
use Zend\Log\Writer\FirePhp\FirePhpInterface;
use Zend\Log\Logger;

/**
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class FirePhpTest extends \PHPUnit_Framework_TestCase
{
    protected $firephp;

    public function setUp()
    {
        $this->firephp = new MockFirePhp();
    }
    /**
     * Test get FirePhp
     */
    public function testGetFirePhp()
    {
        $writer = new FirePhp($this->firephp);
        $this->assertInstanceOf('Zend\Log\Writer\FirePhp\FirePhpInterface', $writer->getFirePhp());
    }
    /**
     * Test set firephp
     */
    public function testSetFirePhp()
    {
        $writer   = new FirePhp($this->firephp);
        $firephp2 = new MockFirePhp();

        $writer->setFirePhp($firephp2);
        $this->assertInstanceOf('Zend\Log\Writer\FirePhp\FirePhpInterface', $writer->getFirePhp());
        $this->assertEquals($firephp2, $writer->getFirePhp());
    }
    /**
     * Test write
     */
    public function testWrite()
    {
        $writer = new FirePhp($this->firephp);
        $writer->write(array(
            'message' => 'my msg',
            'priority' => Logger::DEBUG
        ));
        $this->assertEquals('my msg', $this->firephp->calls['trace'][0]);
    }
    /**
     * Test write with FirePhp disabled
     */
    public function testWriteDisabled()
    {
        $firephp = new MockFirePhp(false);
        $writer = new FirePhp($firephp);
        $writer->write(array(
            'message' => 'my msg',
            'priority' => Logger::DEBUG
        ));
        $this->assertEmpty($this->firephp->calls);
    }

    public function testConstructWithOptions()
    {
        $formatter = new \Zend\Log\Formatter\Simple();
        $filter    = new \Zend\Log\Filter\Mock();
        $writer = new FirePhp(array(
                'filters'   => $filter,
                'formatter' => $formatter,
                'instance'  => $this->firephp,
        ));
        $this->assertInstanceOf('Zend\Log\Writer\FirePhp\FirePhpInterface', $writer->getFirePhp());
        $this->assertAttributeInstanceOf('Zend\Log\Formatter\FirePhp', 'formatter', $writer);

        $filters = self::readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertEquals($filter, $filters[0]);
    }

    /**
     * Verify behavior of __construct when 'instance' is not an FirePhpInterface
     *
     * @expectedException Zend\Log\Exception\InvalidArgumentException
     * @expectedExceptionMessage You must pass a valid FirePhp\FirePhpInterface
     */
    public function testConstructWithInvalidInstance()
    {
        new FirePhp(new \StdClass());
    }
}
