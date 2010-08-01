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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Filter;

use Zend\Filter\FilterChain,
    Zend\Filter\AbstractFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class FilterChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures expected return value from empty filter chain
     *
     * @return void
     */
    public function testEmpty()
    {
        $filter = new FilterChain();
        $value  = 'something';
        $this->assertEquals($value, $filter($value));
    }

    /**
     * Ensures that filters are executed in the expected order (FIFO)
     *
     * @return void
     */
    public function testFilterOrder()
    {
        $filter = new FilterChain();
        $filter->addFilter(new LowerCase())
               ->addFilter(new StripUpperCase());
        $value = 'AbC';
        $valueExpected = 'abc';
        $this->assertEquals($valueExpected, $filter($value));
    }

    /**
     * Ensures that filters can be prepended and will be executed in the
     * expected order
     */
    public function testFilterPrependOrder()
    {
        $filter = new FilterChain();
        $filter->appendFilter(new StripUpperCase())
               ->prependFilter(new LowerCase());
        $value = 'AbC';
        $valueExpected = 'abc';
        $this->assertEquals($valueExpected, $filter($value));
    }
}


class LowerCase extends AbstractFilter
{
    public function filter($value)
    {
        return strtolower($value);
    }
}


class StripUpperCase extends AbstractFilter
{
    public function filter($value)
    {
        return preg_replace('/[A-Z]/', '', $value);
    }
}
