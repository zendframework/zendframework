<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter\Compress;

use Zend\Filter\Compress\Snappy as SnappyCompression;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class SnappyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('snappy')) {
            $this->markTestSkipped('This adapter needs the snappy extension');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        $filter  = new SnappyCompression();

        $content = $filter->compress('compress me');
        $this->assertNotEquals('compress me', $content);

        $content = $filter->decompress($content);
        $this->assertEquals('compress me', $content);
    }

    // TODO test all three warnings (on each method)
    // TODO test exception
    // TODO test recompression
    // TODO test null/ false compression
    // TODO test invalid argument cases

    /**
     * testing toString
     *
     * @return void
     */
    public function testSnappyToString()
    {
        $filter = new SnappyCompression();
        $this->assertEquals('Snappy', $filter->toString());
    }
}
