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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\Callback;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valid = new Callback(array($this, 'objectCallback'));
        $this->assertTrue($valid->isValid('test'));
    }

    public function testStaticCallback()
    {
        $valid = new Callback(
            array('\ZendTest\Validator\CallbackTest', 'staticCallback')
        );
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptionsAfterwards()
    {
        $valid = new Callback(array($this, 'objectCallback'));
        $valid->setCallbackOptions('options');
        $this->assertEquals(array('options'), $valid->getCallbackOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptions()
    {
        $valid = new Callback(array('callback' => array($this, 'objectCallback'), 'callbackOptions' => 'options'));
        $this->assertEquals(array('options'), $valid->getCallbackOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testGettingCallback()
    {
        $valid = new Callback(array($this, 'objectCallback'));
        $this->assertEquals(array($this, 'objectCallback'), $valid->getCallback());
    }

    public function testInvalidCallback()
    {
        $valid = new Callback(array($this, 'objectCallback'));

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Invalid callback given');
        $valid->setCallback('invalidcallback');
    }

    public function testAddingValueOptions()
    {
        $valid = new Callback(array('callback' => array($this, 'optionsCallback'), 'callbackOptions' => 'options'));
        $this->assertEquals(array('options'), $valid->getCallbackOptions());
        $this->assertTrue($valid->isValid('test', 'something'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Callback(array($this, 'objectCallback'));
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testCanAcceptContextWithoutOptions()
    {
        $value     = 'bar';
        $context   = array('foo' => 'bar', 'bar' => 'baz');
        $validator = new Callback(function($v, $c) use ($value, $context) {
            return (($value == $v) && ($context == $c));
        });
        $this->assertTrue($validator->isValid($value, $context));
    }

    public function testCanAcceptContextWithOptions()
    {
        $value     = 'bar';
        $context   = array('foo' => 'bar', 'bar' => 'baz');
        $options   = array('baz' => 'bat');
        $validator = new Callback(function($v, $c, $baz) use ($value, $context, $options) {
            return (($value == $v) && ($context == $c) && ($options['baz'] == $baz));
        });
        $validator->setCallbackOptions($options);
        $this->assertTrue($validator->isValid($value, $context));
    }

    public function objectCallback($value)
    {
        return true;
    }

    public static function staticCallback($value)
    {
        return true;
    }

    public function optionsCallback($value)
    {
        $args = func_get_args();
        $this->assertContains('something', $args);
        return $args;
    }
}
