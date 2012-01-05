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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\Callback as CallbackFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectCallback()
    {
        $filter = new CallbackFilter(array($this, 'objectCallback'));
        $this->assertEquals('objectCallback-test', $filter('test'));
    }

    public function testStaticCallback()
    {
        $filter = new CallbackFilter(
            array(__CLASS__, 'staticCallback')
        );
        $this->assertEquals('staticCallback-test', $filter('test'));
    }

    public function testSettingDefaultOptions()
    {
        $filter = new CallbackFilter(array($this, 'objectCallback'), 'options');
        $this->assertEquals('options', $filter->getOptions());
        $this->assertEquals('objectCallback-test', $filter('test'));
    }

    public function testSettingDefaultOptionsAfterwards()
    {
        $filter = new CallbackFilter(array($this, 'objectCallback'));
        $filter->setOptions('options');
        $this->assertEquals('options', $filter->getOptions());
        $this->assertEquals('objectCallback-test', $filter('test'));
    }

    public function testCallbackWithStringParameter()
    {
        $filter = new CallbackFilter('strrev');
        $this->assertEquals('!olleH', $filter('Hello!'));
    }

    public function testCallbackWithArrayParameters()
    {
        $filter = new CallbackFilter('strrev');
        $this->assertEquals('!olleH', $filter('Hello!'));
    }

    public function objectCallback($value)
    {
        return 'objectCallback-' . $value;
    }

    public static function staticCallback($value)
    {
        return 'staticCallback-' . $value;
    }
}
