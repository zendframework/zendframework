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

use Zend\Filter\Callback as CallbackFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectCallback()
    {
        $filter = new CallbackFilter(array($this, 'objectCallback'));
        $this->assertEquals('objectCallback-test', $filter('test'));
    }

    public function testConstructorWithOptions()
    {
        $filter = new CallbackFilter(array(
            'callback'        => array($this, 'objectCallbackWithParams'),
            'callback_params' => 0,
        ));

        $this->assertEquals('objectCallbackWithParams-test-0', $filter('test'));
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
        $filter = new CallbackFilter(array($this, 'objectCallback'), 'param');
        $this->assertEquals(array('param'), $filter->getCallbackParams());
        $this->assertEquals('objectCallback-test', $filter('test'));
    }

    public function testSettingDefaultOptionsAfterwards()
    {
        $filter = new CallbackFilter(array($this, 'objectCallback'));
        $filter->setCallbackParams('param');
        $this->assertEquals(array('param'), $filter->getCallbackParams());
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

    public function objectCallbackWithParams($value, $param = null)
    {
        return 'objectCallbackWithParams-' . $value . '-' . $param;
    }
}
