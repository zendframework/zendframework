<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter;

use Zend\Filter\BaseName as BaseNameFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class BaseNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $filter = new BaseNameFilter();
        $valuesExpected = array(
            '/path/to/filename'     => 'filename',
            '/path/to/filename.ext' => 'filename.ext'
            );
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter($input));
        }
    }
    
    /**
     * Ensures that an InvalidArgumentException is raised if array is used
     *
     * @return void
     */
    public function testExceptionRaisedIfArrayUsed()
    {
        $filter = new BaseNameFilter();
        $input = array('/path/to/filename', '/path/to/filename.ext');
    
        try {
            $filter->filter($input);
        } catch (\Zend\Filter\Exception\InvalidArgumentException $expected) {
            return;
        }
    
        $this->fail('An expected InvalidArgumentException has not been raised.');
    }
}
