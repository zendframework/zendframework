<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Filter;

use Zend\Log\Filter\Regex;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class RegexTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageFilterRecognizesInvalidRegularExpression()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'invalid reg');
        new Regex('invalid regexp');
    }

    public function testMessageFilter()
    {
        $filter = new Regex('/accept/');
        $this->assertTrue($filter->filter(array('message' => 'foo accept bar')));
        $this->assertFalse($filter->filter(array('message' => 'foo reject bar')));
    }
}
