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

use ZendTest\Log\TestAsset\MockFirePhp;
use Zend\Log\Writer\FirePhp;
use Zend\Log\Writer\FirePhp\FirePhpInterface;
use Zend\Log\Logger;

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
        $this->assertTrue($writer->getFirePhp() instanceof FirePhpInterface);
    }
    /**
     * Test set firephp
     */
    public function testSetFirePhp()
    {
        $writer   = new FirePhp($this->firephp);
        $firephp2 = new MockFirePhp();

        $writer->setFirePhp($firephp2);
        $this->assertTrue($writer->getFirePhp() instanceof FirePhpInterface);
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
        $this->assertTrue(empty($this->firephp->calls));
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
        $this->assertTrue($writer->getFirePhp() instanceof FirePhpInterface);
        $this->assertAttributeInstanceOf('Zend\Log\Formatter\FirePhp', 'formatter', $writer);

        $filters = self::readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertEquals($filter, $filters[0]);
    }
}
