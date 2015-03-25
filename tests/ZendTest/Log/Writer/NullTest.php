<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Writer;

use Zend\Log\Writer\Null as NullWriter;

/**
 * @group      Zend_Log
 */
class NullTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $this->markTestSkipped('Cannot test Null log writer under PHP 7; reserved keyword');
        }
    }

    public function testRaisesNoticeOnInstantiation()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Deprecated');
        new NullWriter();
    }
}
